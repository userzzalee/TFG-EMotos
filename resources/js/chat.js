/**
 * Chat en tiempo real con Laravel Echo + Reverb.
 *
 *  - initBadgeNavbar(): escucha el canal privado del usuario y actualiza el
 *    badge de mensajes no leídos del navbar sin recargar.
 *  - initSalaChat(): en la pantalla de una conversación, recibe mensajes al
 *    instante y envía por AJAX (sin recargar la página).
 *
 * Ambas funciones se activan solo si existen los elementos del DOM y Echo está
 * disponible, así que es seguro importarlas en todas las páginas.
 */

const userId = () => {
    const meta = document.querySelector('meta[name="user-id"]');
    return meta ? parseInt(meta.content, 10) : null;
};

const csrfToken = () =>
    document.querySelector('meta[name="csrf-token"]')?.content ?? '';

/* ------------------------------------------------------------------ */
/* Badge de no leídos en el navbar                                     */
/* ------------------------------------------------------------------ */

function pintarBadge(n) {
    const badge = document.getElementById('chat-badge');
    if (!badge) return;

    if (n > 0) {
        badge.textContent = n > 99 ? '99+' : n;
        badge.classList.remove('hidden');
    } else {
        badge.classList.add('hidden');
    }
}

function leerBadgeActual() {
    const badge = document.getElementById('chat-badge');
    if (!badge || badge.classList.contains('hidden')) return 0;
    return parseInt(badge.textContent, 10) || 0;
}

function initBadgeNavbar() {
    const id = userId();
    if (!id || !window.Echo) return;

    window.Echo.private(`usuario.${id}`).listen('.mensaje.enviado', (e) => {
        // Si el mensaje pertenece a la conversación que está abierta, no cuenta
        // como "no leído": la sala de chat ya lo está mostrando y marcando leído.
        if (window.__conversacionAbierta && e.conversacion_id === window.__conversacionAbierta) {
            return;
        }
        pintarBadge(leerBadgeActual() + 1);
    });
}

/* ------------------------------------------------------------------ */
/* Sala de chat (pantalla de una conversación)                         */
/* ------------------------------------------------------------------ */

function crearBurbuja({ contenido, hora, esMio, id }) {
    const fila = document.createElement('div');
    fila.className = `flex ${esMio ? 'justify-end' : 'justify-start'}`;
    if (id) fila.dataset.msgId = id;

    const burbuja = document.createElement('div');
    burbuja.className =
        'max-w-[70%] px-3 py-2 rounded-[10px] text-xs leading-relaxed ' +
        (esMio
            ? 'bg-[#f0c36d]/15 border border-[#f0c36d]/25 text-[#f0c36d]'
            : 'bg-white/5 border border-white/10 text-gray-300');

    const texto = document.createElement('p');
    texto.textContent = contenido; // textContent => seguro frente a XSS

    const meta = document.createElement('p');
    meta.className = 'text-right mt-1 opacity-40 text-[10px]';
    meta.textContent = hora;

    burbuja.append(texto, meta);
    fila.append(burbuja);
    return fila;
}

function initSalaChat() {
    const app = document.getElementById('chat-app');
    if (!app) return;

    const conversacionId = parseInt(app.dataset.conversacionId, 10);
    const box = document.getElementById('chat-box');
    const form = document.getElementById('chat-form');
    const input = form?.querySelector('input[name="contenido"]');
    const placeholder = document.getElementById('chat-vacio');
    const yo = userId();

    // Marcamos qué conversación está abierta (lo usa el badge del navbar).
    window.__conversacionAbierta = conversacionId;

    const irAlFinal = () => {
        if (box) box.scrollTop = box.scrollHeight;
    };

    const yaPintado = (id) =>
        id && box.querySelector(`[data-msg-id="${id}"]`) !== null;

    const añadirMensaje = (datos) => {
        if (yaPintado(datos.id)) return;
        placeholder?.remove();
        box.appendChild(crearBurbuja(datos));
        irAlFinal();
    };

    irAlFinal();

    /* --- Recibir mensajes en tiempo real --- */
    if (window.Echo) {
        window.Echo.private(`conversacion.${conversacionId}`).listen('.mensaje.enviado', (e) => {
            añadirMensaje({
                id: e.id,
                contenido: e.contenido,
                hora: e.hora,
                esMio: e.remitente_id === yo,
            });

            // Mensaje recibido del otro estando dentro: lo marcamos como leído
            // en el servidor y sincronizamos el badge global.
            if (e.remitente_id !== yo) {
                fetch(`/chat/${conversacionId}/leer`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                        Accept: 'application/json',
                    },
                })
                    .then((r) => r.json())
                    .then((d) => pintarBadge(d.no_leidos))
                    .catch(() => {});
            }
        });
    }

    /* --- Enviar mensajes por AJAX (sin recargar) --- */
    if (form && input) {
        form.addEventListener('submit', async (ev) => {
            ev.preventDefault();
            const contenido = input.value.trim();
            if (!contenido) return;

            input.value = '';
            input.focus();

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-Socket-ID': window.Echo ? window.Echo.socketId() : '',
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                    },
                    body: JSON.stringify({ contenido }),
                });

                if (!res.ok) throw new Error('Error al enviar');

                const data = await res.json();
                añadirMensaje({
                    id: data.id,
                    contenido: data.contenido,
                    hora: data.hora,
                    esMio: true,
                });
            } catch (err) {
                // Si algo falla, devolvemos el texto al input para no perderlo.
                input.value = contenido;
                console.error(err);
            }
        });
    }
}

/* ------------------------------------------------------------------ */

document.addEventListener('DOMContentLoaded', () => {
    initBadgeNavbar();
    initSalaChat();
});
