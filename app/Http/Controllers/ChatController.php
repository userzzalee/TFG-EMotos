<?php

namespace App\Http\Controllers;

use App\Models\Conversacion;
use App\Models\Mensaje;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    // Listar todas las conversaciones del usuario autenticado
    public function index(): View
    {
        $userId = Auth::id();

        $conversaciones = Conversacion::with(['comprador', 'vendedor', 'producto', 'ultimoMensaje'])
            ->where('comprador_id', $userId)
            ->orWhere('vendedor_id', $userId)
            ->orderByDesc('ultimo_mensaje_at')
            ->get();

        return view('chat.index', compact('conversaciones', 'userId'));
    }

    // Abrir o crear conversación al pulsar "Contactar con el vendedor"
    public function iniciar(Producto $producto): RedirectResponse
    {
        $compradorId = Auth::id();
        $vendedorId  = $producto->user_id; // Ajusta al nombre 
        // El vendedor no puede chatear consigo mismo
        if ($compradorId === $vendedorId) {
            return back()->with('error', 'No puedes contactar contigo mismo.');
        }

        $conversacion = Conversacion::firstOrCreate(
            [
                'comprador_id' => $compradorId,
                'vendedor_id'  => $vendedorId,
                'producto_id'  => $producto->id,
            ],
            ['ultimo_mensaje_at' => now()]
        );

        return redirect()->route('chat.show', $conversacion->id);
    }


    // Ver una conversación y sus mensajes
    public function show(Conversacion $conversacion): View
    {
        $userId = Auth::id();

        // Solo los participantes pueden ver la conversación
        abort_unless(
            $conversacion->comprador_id === $userId || $conversacion->vendedor_id === $userId,
            403
        );

        // Marcar como leídos los mensajes del otro
        $conversacion->mensajes()
            ->where('remitente_id', '!=', $userId)
            ->whereNull('leido_at')
            ->update(['leido_at' => now()]);

        $mensajes = $conversacion->mensajes()->with('remitente')->get();
        $otro     = $conversacion->otroParticipante($userId);

        return view('chat.show', compact('conversacion', 'mensajes', 'otro', 'userId'));
    }


    // Enviar un mensaje
    public function enviar(Request $request, Conversacion $conversacion): RedirectResponse
    {
        $userId = Auth::id();

        abort_unless(
            $conversacion->comprador_id === $userId || $conversacion->vendedor_id === $userId,
            403
        );

        $request->validate([
            'contenido' => 'required|string|max:2000',
        ]);

        Mensaje::create([
            'conversacion_id' => $conversacion->id,
            'remitente_id'    => $userId,
            'contenido'       => $request->input('contenido'),
        ]);

        $conversacion->update(['ultimo_mensaje_at' => now()]);

        return redirect()->route('chat.show', $conversacion->id);
    }
}
