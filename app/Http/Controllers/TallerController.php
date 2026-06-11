<?php

namespace App\Http\Controllers;

use App\Models\CitaTaller;
use App\Http\Requests\StoreCitaTallerRequest;
use App\Http\Requests\ComentarCitaRequest;
use App\Http\Requests\FinalizarCitaRequest;
use App\Notifications\CitaEstadoActualizado;
use App\Support\AgendaTaller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Laravel\Cashier\Cashier;

class TallerController extends Controller
{
    // Usuario normal
    public function crear()
    {
        $agenda = AgendaTaller::diasDisponibles();

        return view('taller.usuario.crear', compact('agenda'));
    }

    public function guardar(StoreCitaTallerRequest $request)
    {
        $data = $request->validated();

        $rutas = [];
        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $foto) {
                $rutas[] = $foto->store('taller/fotos', 'public');
            }
        }

        CitaTaller::create([
            'user_id'     => Auth::id(),
            'marca'       => $data['marca'],
            'modelo'      => $data['modelo'],
            'matricula'   => $data['matricula'],
            'fecha_cita'  => $data['fecha_cita'],
            'problema'    => $data['problema'],
            'comentarios' => $data['comentarios'] ?? null,
            'fotos'       => $rutas ?: null,
            'estado'      => 'pendiente',
        ]);

        return redirect()->route('taller.mis-citas')
                         ->with('success', 'Cita creada correctamente. Te avisaremos cuando sea aceptada.');
    }

    public function misCitas()
    {
        $pendientes  = CitaTaller::where('user_id', Auth::id())
                            ->whereIn('estado', ['pendiente', 'aceptada', 'en_proceso'])
                            ->latest()->get();

        $finalizadas = CitaTaller::where('user_id', Auth::id())
                            ->where('estado', 'finalizada')
                            ->latest()->get();

        $pagadas     = CitaTaller::where('user_id', Auth::id())
                            ->where('estado', 'pagada')
                            ->latest()->get();

        return view('taller.usuario.mis-citas', compact('pendientes', 'finalizadas', 'pagadas'));
    }

    public function pagar(Request $request, CitaTaller $cita)
    {
        $this->authorize('pagar', $cita);
        abort_unless($cita->esFinalizada(), 422, 'La cita no está lista para pagar.');

        // Importe en céntimos (Stripe trabaja en la unidad mínima de la moneda).
        $centimos = (int) round(((float) $cita->coste) * 100);

        // Creamos una sesión de Stripe Checkout (página de pago alojada por Stripe)
        // y redirigimos al cliente. Al volver, verificamos el pago en pagoExito().
        return $request->user()->checkoutCharge(
            $centimos,
            'Reparación: ' . $cita->marca . ' ' . $cita->modelo,
            1,
            [
                'success_url' => route('taller.pago.exito', $cita) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'  => route('taller.pago.cancelado', $cita),
            ],
        );
    }

    /**
     * Stripe redirige aquí tras un pago. Verificamos con Stripe que la sesión
     * está realmente pagada antes de marcar la cita como pagada.
     */
    public function pagoExito(Request $request, CitaTaller $cita)
    {
        $this->authorize('pagar', $cita);

        // Si ya estaba pagada (p. ej. al recargar), no hacemos nada más.
        if ($cita->esPagada()) {
            return redirect()->route('taller.mis-citas')->with('success', 'Esta cita ya estaba pagada.');
        }

        $sessionId = $request->query('session_id');

        if (! $sessionId) {
            return redirect()->route('taller.mis-citas')->with('error', 'No se pudo verificar el pago.');
        }

        try {
            $sesion = Cashier::stripe()->checkout->sessions->retrieve($sessionId);
        } catch (\Throwable $e) {
            return redirect()->route('taller.mis-citas')->with('error', 'No se pudo verificar el pago con Stripe.');
        }

        if (($sesion->payment_status ?? null) === 'paid') {
            $cita->update([
                'estado'            => 'pagada',
                'stripe_session_id' => $sessionId,
            ]);

            return redirect()->route('taller.mis-citas')->with('success', 'Pago completado correctamente. ¡Gracias!');
        }

        return redirect()->route('taller.mis-citas')->with('error', 'El pago no se ha completado.');
    }

    /**
     * El cliente canceló el pago en la página de Stripe.
     */
    public function pagoCancelado(CitaTaller $cita)
    {
        $this->authorize('pagar', $cita);

        return redirect()->route('taller.mis-citas')
                         ->with('error', 'Has cancelado el pago. Puedes intentarlo de nuevo cuando quieras.');
    }

    // Mecanico
    public function nuevasCitas()
    {
        $this->authorize('gestionar', CitaTaller::class);

        $citas = CitaTaller::where('estado', 'pendiente')->latest()->get();

        return view('taller.mecanico.nuevas-citas', compact('citas'));
    }

    public function aceptar(CitaTaller $cita)
    {
        $this->authorize('gestionar', $cita);
        abort_unless($cita->esPendiente(), 422);

        $cita->update([
            'estado'      => 'aceptada',
            'mecanico_id' => Auth::id(),
        ]);

        $cita->usuario->notify(new CitaEstadoActualizado($cita));

        return redirect()->route('taller.nuevas-citas')
                         ->with('success', 'Cita aceptada.');
    }

    public function trabajoPendiente()
    {
        $this->authorize('gestionar', CitaTaller::class);

        $citas = CitaTaller::where('mecanico_id', Auth::id())
                    ->whereIn('estado', ['aceptada', 'en_proceso'])
                    ->latest()->get();

        return view('taller.mecanico.trabajo-pendiente', compact('citas'));
    }

    public function detalleCita(CitaTaller $cita)
    {
        $this->authorize('gestionar', $cita);

        return view('taller.mecanico.detalle-cita', compact('cita'));
    }

    public function comentar(ComentarCitaRequest $request, CitaTaller $cita)
    {
        $data = $request->validated();

        $cita->update([
            'comentario_mecanico' => $data['comentario_mecanico'],
            'estado'              => 'en_proceso',
        ]);

        $cita->usuario->notify(new CitaEstadoActualizado($cita));

        return redirect()->route('taller.detalle-cita', $cita)
                         ->with('success', 'Comentario guardado.');
    }

    public function finalizar(FinalizarCitaRequest $request, CitaTaller $cita)
    {
        $data = $request->validated();

        $cita->update([
            'coste'               => $data['coste'],
            'comentario_mecanico' => $data['comentario_mecanico'] ?? $cita->comentario_mecanico,
            'estado'              => 'finalizada',
        ]);

        $cita->usuario->notify(new CitaEstadoActualizado($cita));

        return redirect()->route('taller.trabajo-pendiente')
                         ->with('success', 'Cita finalizada. El cliente podrá realizar el pago.');
    }

    public function historial()
    {
        $this->authorize('gestionar', CitaTaller::class);

        $citas = CitaTaller::where('mecanico_id', Auth::id())
                    ->whereIn('estado', ['finalizada', 'pagada'])
                    ->latest()->get();

        return view('taller.mecanico.historial', compact('citas'));
    }

    public function index()
    {
        $nuevas = CitaTaller::where('estado', 'pendiente')->count();
        
        $enproceso = CitaTaller::where('mecanico_id', Auth::id())
                              ->whereIn('estado', ['aceptada', 'en_proceso'])
                              ->count();
        
        $pendientes = CitaTaller::where('user_id', Auth::id())
                             ->whereIn('estado', ['pendiente', 'aceptada', 'en_proceso'])
                             ->count();
        
        return view('taller.index', compact('nuevas', 'enproceso', 'pendientes'));
    }

}
