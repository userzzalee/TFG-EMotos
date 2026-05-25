<?php

namespace App\Http\Controllers;

use App\Models\CitaTaller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TallerController extends Controller
{
    // ═══════════════════════════════════════════════════════════
    //  USUARIO NORMAL
    // ═══════════════════════════════════════════════════════════

    /** Formulario nueva cita */
    public function crear()
    {
        return view('taller.usuario.crear');
    }

    /** Guardar nueva cita */
    public function guardar(Request $request)
    {
        $data = $request->validate([
            'marca'        => 'required|string|max:100',
            'modelo'       => 'required|string|max:100',
            'matricula'    => 'required|string|max:20',
            'problema'     => 'required|string|max:2000',
            'comentarios'  => 'nullable|string|max:1000',
            'fotos'        => 'nullable|array|max:5',
            'fotos.*'      => 'image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

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
            'problema'    => $data['problema'],
            'comentarios' => $data['comentarios'] ?? null,
            'fotos'       => $rutas ?: null,
            'estado'      => 'pendiente',
        ]);

        return redirect()->route('taller.mis-citas')
                         ->with('success', 'Cita creada correctamente. Te avisaremos cuando sea aceptada.');
    }

    /** Mis citas (usuario) */
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

    /** Pagar cita finalizada (simulado — integrar pasarela real si se requiere) */
    public function pagar(CitaTaller $cita)
    {
        abort_unless($cita->user_id === Auth::id(), 403);
        abort_unless($cita->esFinalizada(), 422, 'La cita no está lista para pagar.');

        $cita->update(['estado' => 'pagada']);

        return redirect()->route('taller.mis-citas')
                         ->with('success', 'Pago registrado correctamente. ¡Gracias!');
    }

    // ═══════════════════════════════════════════════════════════
    //  MECÁNICO
    // ═══════════════════════════════════════════════════════════

    /** Nuevas citas pendientes de aceptar */
    public function nuevasCitas()
    {
        abort_unless(Auth::user()->rol === 'mecanico' || Auth::user()->esAdmin(), 403);

        $citas = CitaTaller::where('estado', 'pendiente')->latest()->get();

        return view('taller.mecanico.nuevas-citas', compact('citas'));
    }

    /** Aceptar una cita */
    public function aceptar(CitaTaller $cita)
    {
        abort_unless(Auth::user()->rol === 'mecanico' || Auth::user()->esAdmin(), 403);
        abort_unless($cita->esPendiente(), 422);

        $cita->update([
            'estado'      => 'aceptada',
            'mecanico_id' => Auth::id(),
        ]);

        return redirect()->route('taller.nuevas-citas')
                         ->with('success', 'Cita aceptada.');
    }

    /** Trabajo pendiente (citas aceptadas/en proceso del mecánico) */
    public function trabajoPendiente()
    {
        abort_unless(Auth::user()->rol === 'mecanico' || Auth::user()->esAdmin(), 403);

        $citas = CitaTaller::where('mecanico_id', Auth::id())
                    ->whereIn('estado', ['aceptada', 'en_proceso'])
                    ->latest()->get();

        return view('taller.mecanico.trabajo-pendiente', compact('citas'));
    }

    /** Ver detalle de una cita (mecánico) */
    public function detalleCita(CitaTaller $cita)
    {
        abort_unless(Auth::user()->rol === 'mecanico' || Auth::user()->esAdmin(), 403);

        return view('taller.mecanico.detalle-cita', compact('cita'));
    }

    /** Guardar comentario del mecánico y/o cambiar estado a en_proceso */
    public function comentar(Request $request, CitaTaller $cita)
    {
        abort_unless(Auth::user()->rol === 'mecanico' || Auth::user()->esAdmin(), 403);
        abort_unless($cita->mecanico_id === Auth::id(), 403);

        $data = $request->validate([
            'comentario_mecanico' => 'required|string|max:2000',
        ]);

        $cita->update([
            'comentario_mecanico' => $data['comentario_mecanico'],
            'estado'              => 'en_proceso',
        ]);

        return redirect()->route('taller.detalle-cita', $cita)
                         ->with('success', 'Comentario guardado.');
    }

    /** Finalizar cita con coste */
    public function finalizar(Request $request, CitaTaller $cita)
    {
        abort_unless(Auth::user()->rol === 'mecanico' || Auth::user()->esAdmin(), 403);
        abort_unless($cita->mecanico_id === Auth::id(), 403);

        $data = $request->validate([
            'coste'               => 'required|numeric|min:0',
            'comentario_mecanico' => 'nullable|string|max:2000',
        ]);

        $cita->update([
            'coste'               => $data['coste'],
            'comentario_mecanico' => $data['comentario_mecanico'] ?? $cita->comentario_mecanico,
            'estado'              => 'finalizada',
        ]);

        return redirect()->route('taller.trabajo-pendiente')
                         ->with('success', 'Cita finalizada. El cliente podrá realizar el pago.');
    }

    /** Historial de trabajo (citas completadas del mecánico) */
    public function historial()
    {
        abort_unless(Auth::user()->rol === 'mecanico' || Auth::user()->esAdmin(), 403);

        $citas = CitaTaller::where('mecanico_id', Auth::id())
                    ->whereIn('estado', ['finalizada', 'pagada'])
                    ->latest()->get();

        return view('taller.mecanico.historial', compact('citas'));
    }

    // ── Página principal del taller ──────────────────────────────────────────────
    public function index()
    {
        return view('taller.index');
    }

}

    // Añadir este método al TallerController existente
