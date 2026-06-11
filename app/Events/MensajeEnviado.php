<?php

namespace App\Events;

use App\Models\Mensaje;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Se dispara cada vez que se envía un mensaje en una conversación.
 *
 * Implementa ShouldBroadcastNow (en lugar de ShouldBroadcast) para emitirse
 * de forma síncrona dentro de la petición: así NO hace falta tener un worker
 * de colas corriendo para que el chat funcione en tiempo real.
 */
class MensajeEnviado implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Mensaje $mensaje,
        public int $destinatarioId,
    ) {
    }

    /**
     * Canales por los que se emite el evento.
     *
     * - conversacion.{id}: lo escuchan los dos participantes mientras tienen
     *   abierta la ventana de chat (mensajes instantáneos).
     * - usuario.{id}: canal privado del destinatario, para el badge de
     *   mensajes no leídos del navbar (esté donde esté navegando).
     *
     * @return array<int, PrivateChannel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('conversacion.' . $this->mensaje->conversacion_id),
            new PrivateChannel('usuario.' . $this->destinatarioId),
        ];
    }

    /**
     * Nombre del evento que se escucha en el frontend con Echo (.mensaje.enviado).
     */
    public function broadcastAs(): string
    {
        return 'mensaje.enviado';
    }

    /**
     * Datos que viajan al cliente. Mandamos solo lo necesario para pintar
     * la burbuja sin tener que volver a consultar al servidor.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id'               => $this->mensaje->id,
            'conversacion_id'  => $this->mensaje->conversacion_id,
            'remitente_id'     => $this->mensaje->remitente_id,
            'remitente_nombre' => $this->mensaje->remitente?->name,
            'contenido'        => $this->mensaje->contenido,
            'hora'             => $this->mensaje->created_at->format('H:i'),
            'created_at'       => $this->mensaje->created_at->toIso8601String(),
        ];
    }
}
