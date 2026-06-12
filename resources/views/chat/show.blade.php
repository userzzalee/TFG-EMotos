@extends('layouts.general')

@section('content')

{{-- Cabecera --}}
<div class="flex items-center gap-3 mb-4 sm:mb-6">
    <a href="{{ route('chat.index') }}"
       class="text-[10px] uppercase tracking-widest text-white/40 hover:text-[#f0c36d] transition-colors shrink-0">
       ← Volver
    </a>
    <div class="w-px h-4 bg-white/10 shrink-0"></div>
    <div class="w-8 h-8 rounded-full bg-[#f0c36d]/10 border border-[#f0c36d]/20 flex items-center justify-center text-[#f0c36d] text-xs font-bold uppercase shrink-0">
        {{ mb_substr($otro->name, 0, 1) }}
    </div>
    <div class="min-w-0">
        <p class="text-sm font-bold uppercase tracking-widest text-white leading-none truncate">{{ $otro->name }}</p>
        @if($conversacion->tituloArticulo())
            <p class="text-[10px] text-white/30 mt-0.5 uppercase tracking-wider truncate">Sobre: {{ $conversacion->tituloArticulo() }}</p>
        @endif
    </div>
</div>

{{-- Contenedor chat --}}
<div id="chat-app"
     data-conversacion-id="{{ $conversacion->id }}"
     class="flex flex-col"
     style="height: calc(100dvh - 180px); min-height: 300px;">

    {{-- Mensajes --}}
    <div id="chat-box" class="flex flex-col gap-2 overflow-y-auto flex-1 pr-1 mb-3">

        @if($mensajes->hasMorePages())
            <div class="text-center mb-2">
                <a href="{{ $mensajes->nextPageUrl() }}"
                   class="text-xs text-white/40 hover:text-[#f0c36d] transition-colors">
                   ↑ Cargar mensajes anteriores
                </a>
            </div>
        @endif

        @forelse($mensajes as $msg)
            @php $esMio = $msg->remitente_id === $userId; @endphp
            <div class="flex {{ $esMio ? 'justify-end' : 'justify-start' }}" data-msg-id="{{ $msg->id }}">
                <div class="max-w-[85%] sm:max-w-[70%] px-3 py-2 rounded-[10px] text-xs leading-relaxed
                    {{ $esMio
                        ? 'bg-[#f0c36d]/15 border border-[#f0c36d]/25 text-[#f0c36d]'
                        : 'bg-white/5 border border-white/10 text-gray-300' }}">
                    <p class="break-words">{{ $msg->contenido }}</p>
                    <p class="text-right mt-1 opacity-40 text-[10px]">{{ $msg->created_at->format('H:i') }}</p>
                </div>
            </div>
        @empty
            <div id="chat-vacio" class="flex-1 flex items-center justify-center">
                <p class="text-xs text-white/25 uppercase tracking-widest">Sé el primero en escribir.</p>
            </div>
        @endforelse
    </div>

    {{-- Formulario --}}
    <div class="shrink-0 pb-2">
        <form id="chat-form"
              action="{{ route('chat.enviar', $conversacion->id) }}"
              method="POST"
              class="flex gap-2">
            @csrf
            <input
                type="text"
                name="contenido"
                id="chat-input"
                autocomplete="off"
                autofocus
                placeholder="Escribe un mensaje..."
                required
                class="flex-1 min-w-0 bg-white/5 border border-white/10
                       rounded-[8px] px-3 py-2.5 text-xs text-gray-200 placeholder-white/25
                       focus:outline-none focus:border-[#f0c36d]/40 transition-colors"
            >
            <button type="submit"
                    id="chat-submit"
                    class="px-4 py-2.5 rounded-[8px] bg-[#f0c36d]/15 border border-[#f0c36d]/30
                           text-[#f0c36d] text-xs uppercase tracking-widest font-bold
                           hover:bg-[#f0c36d]/25 hover:border-[#f0c36d]/50
                           active:scale-95 transition-all duration-150 shrink-0">
                Enviar
            </button>
        </form>
    </div>

</div>

<script>
(function () {
    const box     = document.getElementById('chat-box');
    const form    = document.getElementById('chat-form');
    const input   = document.getElementById('chat-input');
    const btn     = document.getElementById('chat-submit');
    const app     = document.getElementById('chat-app');
    const yo      = {{ $userId }};
    const convId  = {{ $conversacion->id }};
    const csrf    = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    // Ir al fondo inmediatamente, sin animación
    box.scrollTop = box.scrollHeight;

    window.__conversacionAbierta = convId;

    // Crear burbuja de mensaje
    function burbuja(contenido, hora, esMio, id) {
        const fila = document.createElement('div');
        fila.className = 'flex ' + (esMio ? 'justify-end' : 'justify-start');
        if (id) fila.dataset.msgId = id;

        const bbl = document.createElement('div');
        bbl.className = 'max-w-[85%] sm:max-w-[70%] px-3 py-2 rounded-[10px] text-xs leading-relaxed ' +
            (esMio
                ? 'bg-[#f0c36d]/15 border border-[#f0c36d]/25 text-[#f0c36d]'
                : 'bg-white/5 border border-white/10 text-gray-300');

        const p = document.createElement('p');
        p.className = 'break-words';
        p.textContent = contenido;

        const h = document.createElement('p');
        h.className = 'text-right mt-1 opacity-40 text-[10px]';
        h.textContent = hora;

        bbl.append(p, h);
        fila.append(bbl);
        return fila;
    }

    function yaPintado(id) {
        return id && box.querySelector('[data-msg-id="' + id + '"]');
    }

    function añadir(contenido, hora, esMio, id) {
        if (yaPintado(id)) return;
        document.getElementById('chat-vacio')?.remove();
        box.appendChild(burbuja(contenido, hora, esMio, id));
        box.scrollTop = box.scrollHeight;
    }

    // WebSocket — recibir mensajes al instante
    if (window.Echo) {
        window.Echo.private('conversacion.' + convId).listen('.mensaje.enviado', function (e) {
            añadir(e.contenido, e.hora, e.remitente_id === yo, e.id);

            if (e.remitente_id !== yo) {
                fetch('/chat/' + convId + '/leer', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
                }).then(r => r.json()).then(d => {
                    const badge = document.getElementById('chat-badge');
                    if (badge) badge.style.display = d.no_leidos > 0 ? 'block' : 'none';
                }).catch(() => {});
            }
        });
    }

    // Enviar mensaje por AJAX — aparece al instante sin esperar respuesta del servidor
    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const texto = input.value.trim();
        if (!texto) return;

        // Pintar la burbuja YA, antes de enviar al servidor (optimistic UI)
        const ahora = new Date();
        const hora  = ahora.getHours().toString().padStart(2, '0') + ':' + ahora.getMinutes().toString().padStart(2, '0');
        input.value = '';
        input.focus();
        btn.disabled = true;

        añadir(texto, hora, true, null);

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-Socket-ID': window.Echo ? window.Echo.socketId() : '',
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
                body: JSON.stringify({ contenido: texto }),
            });

            if (!res.ok) throw new Error('fail');

            const data = await res.json();
            // Actualizar el data-msg-id de la burbuja optimista (la última sin id)
            const sinId = [...box.querySelectorAll('[data-msg-id=""]')].pop()
                       || [...box.querySelectorAll('.justify-end:not([data-msg-id])')].pop();
            if (sinId) sinId.dataset.msgId = data.id;

        } catch {
            // Si falla, devolver el texto al input
            input.value = texto;
        } finally {
            btn.disabled = false;
        }
    });

    // Enter para enviar
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
        }
    });
})();
</script>

@endsection
