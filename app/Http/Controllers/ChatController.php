<?php

namespace App\Http\Controllers;

use App\Events\MensajeEnviado;
use App\Http\Requests\SendMessageRequest;
use App\Models\Conversacion;
use App\Models\Mensaje;
use App\Models\Producto;
use App\Models\User;
use App\Notifications\NuevoMensajeChat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();

        $conversaciones = Conversacion::with([
                'comprador:id,name',
                'vendedor:id,name',
                'producto:id,nombre,imagen',
                'anuncio:id,titulo,imagen',
                'ultimoMensaje',
            ])
            ->where('comprador_id', $userId)
            ->orWhere('vendedor_id', $userId)
            ->orderByDesc('ultimo_mensaje_at')
            ->get();

        return view('chat.index', compact('conversaciones', 'userId'));
    }

    public function iniciar(Producto $producto): RedirectResponse
    {
        $compradorId = Auth::id();
        $vendedorId  = $producto->user_id;

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

    public function show(Conversacion $conversacion): View
    {
        $userId = Auth::id();

        $this->authorize('participar', $conversacion);

        // Marcar leídos en una sola query sin cargar objetos
        $conversacion->mensajes()
            ->where('remitente_id', '!=', $userId)
            ->whereNull('leido_at')
            ->update(['leido_at' => now()]);

        // Marcar notificaciones leídas sin traer todas a memoria
        Auth::user()->unreadNotifications()
            ->where('type', NuevoMensajeChat::class)
            ->whereRaw("(data->>'conversacion_id')::integer = ?", [$conversacion->id])
            ->update(['read_at' => now()]);

        // Mensajes en orden cronológico directo, sin reverse(), sin with(remitente)
        // (el remitente_id ya lo tenemos para saber si es mío)
        $mensajes = $conversacion->mensajes()
            ->select('id', 'remitente_id', 'contenido', 'created_at')
            ->orderBy('created_at')
            ->simplePaginate(50);

        $otro = $conversacion->otroParticipante($userId);

        return view('chat.show', compact('conversacion', 'mensajes', 'otro', 'userId'));
    }

    public function enviar(SendMessageRequest $request, Conversacion $conversacion): JsonResponse|RedirectResponse
    {
        $userId = Auth::id();

        $this->authorize('participar', $conversacion);

        $request->validated();

        $mensaje = Mensaje::create([
            'conversacion_id' => $conversacion->id,
            'remitente_id'    => $userId,
            'contenido'       => $request->input('contenido'),
        ]);

        // Actualizar timestamp en background (no bloquea la respuesta)
        $conversacion->timestamps = false;
        $conversacion->update(['ultimo_mensaje_at' => now()]);

        $destinatarioId = $conversacion->comprador_id === $userId
            ? $conversacion->vendedor_id
            : $conversacion->comprador_id;

        // Broadcast síncrono (ShouldBroadcastNow ya está en el evento)
        broadcast(new MensajeEnviado($mensaje->load('remitente'), $destinatarioId))->toOthers();

        // Notificación al destinatario
        User::find($destinatarioId)?->notify(new NuevoMensajeChat($mensaje));

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'id'        => $mensaje->id,
                'contenido' => $mensaje->contenido,
                'hora'      => $mensaje->created_at->format('H:i'),
            ], 201);
        }

        return back();
    }

    public function leer(Conversacion $conversacion): JsonResponse
    {
        $userId = Auth::id();

        $this->authorize('participar', $conversacion);

        $conversacion->mensajes()
            ->where('remitente_id', '!=', $userId)
            ->whereNull('leido_at')
            ->update(['leido_at' => now()]);

        Auth::user()->unreadNotifications()
            ->where('type', NuevoMensajeChat::class)
            ->whereRaw("(data->>'conversacion_id')::integer = ?", [$conversacion->id])
            ->update(['read_at' => now()]);

        return response()->json(['no_leidos' => $this->totalNoLeidos($userId)]);
    }

    public function contadorNoLeidos(): JsonResponse
    {
        return response()->json(['no_leidos' => $this->totalNoLeidos(Auth::id())]);
    }

    private function totalNoLeidos(int $userId): int
    {
        return Mensaje::whereHas('conversacion', function ($q) use ($userId) {
            $q->where('comprador_id', $userId)->orWhere('vendedor_id', $userId);
        })
            ->where('remitente_id', '!=', $userId)
            ->whereNull('leido_at')
            ->count();
    }
}
