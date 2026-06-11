<?php

namespace App\Notifications;

use App\Models\Mensaje;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

/**
 * Avisa al destinatario de que ha recibido un mensaje de chat.
 *
 * Canales:
 *  - database  → notificación in-app (campana del navbar).
 *  - broadcast → empuja la notificación por WebSocket para la campana en vivo.
 *
 * No mandamos email por cada mensaje para no saturar el correo. El chat ya
 * depende de Reverb (paso 1), así que usar broadcast aquí no añade requisitos.
 */
class NuevoMensajeChat extends Notification
{
    use Queueable;

    public function __construct(public Mensaje $mensaje)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'tipo'            => 'mensaje',
            'conversacion_id' => $this->mensaje->conversacion_id,
            'remitente'       => $this->mensaje->remitente?->name,
            'icono'           => '💬',
            'titulo'          => 'Nuevo mensaje de ' . ($this->mensaje->remitente?->name ?? 'un usuario'),
            'mensaje'         => Str::limit($this->mensaje->contenido, 80),
            'url'             => route('chat.show', $this->mensaje->conversacion_id),
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
