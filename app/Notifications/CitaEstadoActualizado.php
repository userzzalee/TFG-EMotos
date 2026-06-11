<?php

namespace App\Notifications;

use App\Models\CitaTaller;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Avisa al cliente de que su cita de taller ha cambiado de estado.
 *
 * Canales:
 *  - mail     → correo (en local cae al log: storage/logs/laravel.log).
 *  - database → notificación in-app (campana del navbar; aparece al recargar).
 *
 * Es síncrona y NO usa el canal broadcast a propósito: así aceptar/finalizar
 * una cita nunca depende de que el servidor Reverb esté arriba ni de un worker
 * de colas. (Estas acciones no necesitan ser "en vivo".)
 */
class CitaEstadoActualizado extends Notification
{
    use Queueable;

    public function __construct(public CitaTaller $cita)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        [$titulo, $linea] = $this->textos();

        $mail = (new MailMessage)
            ->subject('Taller AlyX — ' . $titulo)
            ->greeting('Hola ' . $notifiable->name)
            ->line($linea)
            ->line('Moto: ' . $this->cita->marca . ' ' . $this->cita->modelo . ' (' . $this->cita->matricula . ')');

        if ($this->cita->esFinalizada() && $this->cita->coste !== null) {
            $mail->line('Coste del servicio: ' . number_format((float) $this->cita->coste, 2) . ' €');
        }

        if ($this->cita->comentario_mecanico) {
            $mail->line('Comentario del mecánico: ' . $this->cita->comentario_mecanico);
        }

        return $mail
            ->action('Ver mis citas', route('taller.mis-citas'))
            ->line('Gracias por confiar en el taller AlyX.');
    }

    /**
     * Datos que se guardan en la tabla notifications (in-app).
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        [$titulo, $linea] = $this->textos();

        return [
            'tipo'     => 'cita',
            'cita_id'  => $this->cita->id,
            'estado'   => $this->cita->estado,
            'icono'    => '🔧',
            'titulo'   => $titulo,
            'mensaje'  => $linea,
            'url'      => route('taller.mis-citas'),
        ];
    }

    /**
     * Devuelve [titulo, linea] según el estado actual de la cita.
     *
     * @return array{0: string, 1: string}
     */
    private function textos(): array
    {
        $moto = $this->cita->marca . ' ' . $this->cita->modelo;

        return match ($this->cita->estado) {
            'aceptada' => [
                'Tu cita ha sido aceptada',
                "Un mecánico ha aceptado tu cita para tu {$moto}. Pronto empezará a trabajar en ella.",
            ],
            'en_proceso' => [
                'Tu cita está en proceso',
                "El mecánico ya está trabajando en tu {$moto}.",
            ],
            'finalizada' => [
                'Tu cita ha finalizado',
                "El trabajo en tu {$moto} ha finalizado. Ya puedes proceder al pago desde tus citas.",
            ],
            default => [
                'Tu cita se ha actualizado',
                "El estado de tu cita para tu {$moto} es ahora: " . $this->cita->etiquetaEstado() . '.',
            ],
        };
    }
}
