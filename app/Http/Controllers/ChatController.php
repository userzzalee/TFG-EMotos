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
    // Listar todas las conversaciones del usuario autenticado
    public function index(): View
    {
        $userId = Auth::id();

        $conversaciones = Conversacion::with(['comprador:id,name', 'vendedor:id,name', 'producto:id,nombre,imagen', 'anuncio:id,titulo,imagen', 'ultimoMensaje'])
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
        $this->authorize('participar', $conversacion);

        // Marcar como leídos los mensajes del otro
        $conversacion->mensajes()
            ->where('remitente_id', '!=', $userId)
            ->whereNull('leido_at')
            ->update(['leido_at' => now()]);

        // Y también las notificaciones in-app de esos mensajes (campana del navbar).
        $this->marcarNotificacionesMensajeLeidas(Auth::user(), $conversacion->id);

        // Paginamos de más nuevo a más antiguo: así la página 1 trae siempre
        // los mensajes más recientes (lo que el usuario quiere ver al abrir el
        // chat) y "Cargar mensajes anteriores" avanza a páginas superiores con
        // mensajes cada vez más antiguos. Luego invertimos la colección de cada
        // página para pintarla en orden cronológico (antiguo arriba, nuevo abajo).
        $mensajes = $conversacion->mensajes()->with('remitente')->reorder()->latest()->paginate(50);
        $mensajes->setCollection($mensajes->getCollection()->reverse()->values());

        $otro     = $conversacion->otroParticipante($userId);

        return view('chat.show', compact('conversacion', 'mensajes', 'otro', 'userId'));
    }

    // Enviar un mensaje
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

        $conversacion->update(['ultimo_mensaje_at' => now()]);

        // El destinatario es el otro participante de la conversación.
        $destinatarioId = $conversacion->comprador_id === $userId
            ? $conversacion->vendedor_id
            : $conversacion->comprador_id;

        // Emitimos por WebSocket. ->toOthers() evita que el propio emisor
        // reciba su mensaje por duplicado (él ya lo pinta con la respuesta JSON).
        broadcast(new MensajeEnviado($mensaje->load('remitente'), $destinatarioId))->toOthers();

        // Notificación in-app (campana del navbar) para el destinatario.
        $destinatario = User::find($destinatarioId);
        $destinatario?->notify(new NuevoMensajeChat($mensaje));

        // Si es una petición AJAX (chat en tiempo real) devolvemos JSON.
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'id'         => $mensaje->id,
                'contenido'  => $mensaje->contenido,
                'hora'       => $mensaje->created_at->format('H:i'),
                'created_at' => $mensaje->created_at->toIso8601String(),
            ], 201);
        }

        // Fallback sin JavaScript: recargamos como antes.
        return back();
    }

    /**
     * Marca como leídos los mensajes de una conversación y devuelve el total
     * global de no leídos del usuario. Lo llama el chat por AJAX cuando llega
     * un mensaje nuevo mientras la conversación está abierta.
     */
    public function leer(Conversacion $conversacion): JsonResponse
    {
        $userId = Auth::id();

        $this->authorize('participar', $conversacion);

        $conversacion->mensajes()
            ->where('remitente_id', '!=', $userId)
            ->whereNull('leido_at')
            ->update(['leido_at' => now()]);

        $this->marcarNotificacionesMensajeLeidas(Auth::user(), $conversacion->id);

        return response()->json(['no_leidos' => $this->totalNoLeidos($userId)]);
    }

    /**
     * Marca como leídas las notificaciones in-app de mensajes de una
     * conversación concreta (para que la campana no muestre como pendientes
     * mensajes que el usuario ya ha visto en el chat).
     */
    private function marcarNotificacionesMensajeLeidas(User $user, int $conversacionId): void
    {
        $user->unreadNotifications()
            ->where('type', NuevoMensajeChat::class)
            ->get()
            ->filter(fn ($n) => ($n->data['conversacion_id'] ?? null) === $conversacionId)
            ->each
            ->markAsRead();
    }

    /**
     * Devuelve el total de mensajes no leídos del usuario (para el badge del navbar).
     */
    public function contadorNoLeidos(): JsonResponse
    {
        return response()->json(['no_leidos' => $this->totalNoLeidos(Auth::id())]);
    }

    /**
     * Cuenta los mensajes no leídos del usuario en todas sus conversaciones.
     */
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
