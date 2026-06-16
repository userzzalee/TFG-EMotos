/**
 * Chat en tiempo real con Laravel Echo + Reverb.
 *
 *  - initBadgeNavbar(): escucha el canal privado del usuario y muestra/oculta
 *    el punto de mensajes no leídos del navbar sin recargar.
 *  - initSalaChat(): en la pantalla de una conversación, envía con UI optimista
 *    (el mensaje aparece al instante) y recibe los del otro al momento por
 *    WebSocket. ES LA ÚNICA fuente de verdad de la sala: la vista ya no lleva
 *    ningún <script> propio (antes había uno duplicado que provocaba el doble
 *    envío y los mensajes repetidos).
 *
 * Ambas funciones se activan solo si existen sus elementos del DOM y Echo está
 * disponible, así que es seguro importarlas en todas las páginas.
 */

const userId = () => {
    const meta = document.querySelector('meta[name="user-id"]');
    return meta ? parseInt(meta.content, 10) : null;
};

const csrfToken = () =>
    document.querySelector('meta[name="csrf-token"]')?.content ?? '';

/* ------------------------------------------------------------------ */
/* Punto de no leídos en el navbar                                     */
/* ------------------------------------------------------------------ */
/*
 * El badge del navbar es un PUNTO (sin número) que se oculta con
 * `style.display:none`. Por eso lo mostramos/ocultamos por display y no con
 * la clase .hidden: un estilo inline gana a la clase y el punto nunca llegaba
 * a verse en vivo.
 */

function pintarBadge(n) {
    const badge = document.getElementById('chat-badge');
    if (!badge) return;
    badge.style.display = n > 0 ? 'block' : 'none';
}

function hayNoLeidos() {
    const badge = document.getElementById('chat-badge');
    if (!badge) return false;
    return badge.style.display !== 'none';
}

function initBadgeNavbar() {
    const id = userId();
    if (!id || !window.Echo) return;

    window.Echo.private(`usuario.${id}`).listen('.mensaje.enviado', (e) => {
        // Si el mensaje pertenece a la conversación abierta, no cuenta como
        // "no leído": la sala ya lo muestra y lo marca leído.
        if (window.__conversacionAbierta && e.conversacion_id === window.__conversacionAbierta) {
            return;
        }
        pintarBadge(1);
    });
}

/* ------------------------------------------------------------------ */
/* Sala de chat (pantalla de una conversación)                         */
/* ------------------------------------------------------------------ */

function crearBurbuja({ contenido, hora, esMio, id, pendiente = false }) {
    const fila = document.createElement('div');
    fila.className = `flex ${esMio ? 'justify-end' : 'justify-start'} chat-entra`;
    if (id != null) fila.dataset.msgId = id;
    if (pendiente) fila.classList.add('chat-pendiente');

    const burbuja = document.createElement('div');
    burbuja.className =
        'max-w-[85%] sm:max-w-[70%] px-3 py-2 rounded-[10px] text-xs leading-relaxed ' +
        (esMio
            ? 'bg-[#f0c36d]/15 border border-[#f0c36d]/25 text-[#f0c36d]'
            : 'bg-white/5 border border-white/10 text-gray-300');

    const texto = document.createElement('p');
    texto.className = 'break-words';
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
    const box   = document.getElementById('chat-box');
    const form  = document.getElementById('chat-form');
    const input = form?.querySelector('input[name="contenido"]');
    const btn   = document.getElementById('chat-submit');
    const yo    = userId();

    if (!box || !form || !input) return;

    // El badge del navbar usa esto para saber qué conversación está abierta.
    window.__conversacionAbierta = conversacionId;

    /* --- helpers de scroll y pintado --- */

    // ¿El usuario está leyendo el final? (si está arriba leyendo historial no
    // le damos el tirón hacia abajo al llegar un mensaje ajeno).
    const cercaDelFondo = () =>
        box.scrollHeight - box.scrollTop - box.clientHeight < 120;

    const irAlFondo = (suave = false) =>
        box.scrollTo({ top: box.scrollHeight, behavior: suave ? 'smooth' : 'auto' });

    const yaPintado = (id) =>
        id != null && box.querySelector(`[data-msg-id="${id}"]`) !== null;

    const añadirMensaje = (datos, { pendiente = false } = {}) => {
        if (yaPintado(datos.id)) return null;
        document.getElementById('chat-vacio')?.remove();

        const pegado = cercaDelFondo();
        const fila = crearBurbuja({ ...datos, pendiente });
        box.appendChild(fila);

        if (pegado || datos.esMio) irAlFondo(true);
        return fila;
    };

    irAlFondo();

    /* --- Recibir mensajes en tiempo real --- */
    if (window.Echo) {
        window.Echo.private(`conversacion.${conversacionId}`).listen('.mensaje.enviado', (e) => {
            // Mis propios mensajes ya los pinto al enviarlos (UI optimista).
            // Ignoramos el eco para no duplicar ni provocar carreras de IDs.
            if (e.remitente_id === yo) return;

            añadirMensaje({ id: e.id, contenido: e.contenido, hora: e.hora, esMio: false });

            // Estoy dentro: marco leído en el servidor y sincronizo el badge.
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
        });
    }

    /* --- Enviar mensajes (UI optimista: aparece al instante) --- */
    let enviando = false;

    const enviar = async () => {
        const contenido = input.value.trim();
        if (!contenido || enviando) return;

        enviando = true;
        input.value = '';
        input.focus();
        if (btn) btn.disabled = true;

        const ahora = new Date();
        const hora =
            String(ahora.getHours()).padStart(2, '0') + ':' +
            String(ahora.getMinutes()).padStart(2, '0');

        // Burbuja optimista: sin id todavía y marcada como pendiente.
        const fila = añadirMensaje(
            { id: null, contenido, hora, esMio: true },
            { pendiente: true },
        );

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                    // Excluye nuestro propio socket del broadcast (toOthers()).
                    'X-Socket-ID': window.Echo ? window.Echo.socketId() : '',
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
                body: JSON.stringify({ contenido }),
            });

            if (!res.ok) throw new Error('Error al enviar');

            const data = await res.json();
            if (fila) {
                fila.dataset.msgId = data.id; // confirmada por el servidor
                fila.classList.remove('chat-pendiente');
            }
        } catch (err) {
            // Falló: marcamos la burbuja y devolvemos el texto al input.
            if (fila) {
                fila.classList.remove('chat-pendiente');
                fila.classList.add('chat-fallido');
            }
            input.value = contenido;
            console.error(err);
        } finally {
            enviando = false;
            if (btn) btn.disabled = false;
        }
    };

    form.addEventListener('submit', (ev) => {
        ev.preventDefault();
        enviar();
    });
}

/* ------------------------------------------------------------------ */

document.addEventListener('DOMContentLoaded', () => {
    initBadgeNavbar();
    initSalaChat();
});
