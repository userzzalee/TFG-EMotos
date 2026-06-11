import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

/**
 * Configuración de Laravel Echo sobre Reverb (protocolo Pusher).
 *
 * Las variables VITE_REVERB_* se leen del .env en tiempo de build de Vite.
 * Si no están definidas, no inicializamos Echo para no romper páginas que
 * no usan WebSockets.
 */
const reverbKey = import.meta.env.VITE_REVERB_APP_KEY;

if (reverbKey) {
    // Token CSRF para autenticar la suscripción a canales privados (/broadcasting/auth).
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: reverbKey,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
        auth: {
            headers: {
                'X-CSRF-TOKEN': csrf,
            },
        },
    });
}
