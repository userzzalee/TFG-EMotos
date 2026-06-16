import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

/**
 * Configuración de Laravel Echo sobre Pusher Channels.
 *
 * Las variables VITE_PUSHER_* se leen del .env en tiempo de build de Vite.
 * Si no están definidas, no inicializamos Echo para no romper páginas que
 * no usan WebSockets.
 */
const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;

if (pusherKey) {
    // Token CSRF para autenticar la suscripción a canales privados (/broadcasting/auth).
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: pusherKey,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'eu',
        forceTLS: true,
        auth: {
            headers: {
                'X-CSRF-TOKEN': csrf,
            },
        },
    });
}
