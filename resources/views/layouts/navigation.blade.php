<!-- Controla estado del menu movil -->
<nav x-data="{ open: false }"
    class="fixed top-0 left-0 w-full h-[60px] z-[1000] grid grid-cols-[1fr_auto_1fr] items-center px-[40px]
            {{ request()->is('/') ? 'bg-gradient-to-b from-black/100 via-black/70 to-black/0' : 'bg-black' }}">

    {{-- Controles izquierda --}}
    <div class="flex items-center gap-4">
        @auth
            @if(Auth::user()->rol === 'mecanico' || Auth::user()->esAdmin())
                <a href="{{ route('taller.nuevas-citas') }}" class="text-[#ddd] no-underline text-[12px] uppercase tracking-widest hover:text-[#f0c36d] transition-colors">Taller</a>
            @else
                <a href="{{ route('taller.crear') }}" class="text-[#ddd] no-underline text-[12px] uppercase tracking-widest hover:text-[#f0c36d] transition-colors">Taller</a>
            @endif
        @else
            <a href="{{ route('login') }}" class="text-[#ddd] no-underline text-[12px] uppercase tracking-widest hover:text-[#f0c36d] transition-colors">Taller</a>
        @endauth
        <a href="{{ route('merchandising') }}" class="text-[#ddd] no-underline text-[12px] uppercase tracking-widest hover:text-[#f0c36d] transition-colors">Merchandising</a>
        <a href="{{ route('segundamano.index') }}" class="text-[#ddd] no-underline text-[12px] uppercase tracking-widest hover:text-[#f0c36d] transition-colors">Segunda Mano</a>
        @auth
            @if(Auth::user()->esAdmin())
                <a href="{{ route('admin.dashboard') }}" class="text-[#f0c36d] no-underline text-[12px] uppercase tracking-widest hover:text-yellow-300 transition-colors">Panel</a>
            @endif
        @endauth
    </div>

    {{-- Logo --}}
    <div class="justify-self-center">
        <a href="{{ url('/') }}" class="text-white font-bold text-[22px] no-underline tracking-widest">Aly<span class="text-[#f0c36d]">X</span></a>
    </div>

    {{-- Controlores derecha --}}
    <div class="justify-self-end flex items-center gap-4">
        @auth
            {{-- Campana de notificaciones --}}
            @php
                $notiNoLeidas = Auth::user()->unreadNotifications()->count();
            @endphp
            <div x-data="notificaciones({ noLeidas: {{ $notiNoLeidas }}, userId: {{ Auth::id() }}, open: false })"
                 x-init="init()" class="relative flex items-center">

                <button @click="toggle()" type="button"
                    class="relative text-[#ddd] hover:text-[#f0c36d] transition-colors flex items-center"
                    aria-label="Notificaciones">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px]" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                    </svg>
                    <span x-show="noLeidas > 0" x-cloak
                          x-text="noLeidas > 99 ? '99+' : noLeidas"
                          class="absolute -top-2 -right-3 min-w-[16px] h-4 px-1 rounded-full bg-yellow-500 text-black text-[10px] font-bold leading-4 text-center"></span>
                </button>

                {{-- Dropdown --}}
                <div x-show="open" x-cloak @click.outside="open = false"
                     x-transition.origin.top.right
                     class="absolute top-full right-0 mt-3 w-80 max-h-[420px] overflow-y-auto rounded-[10px] border border-white/10 bg-[#111] shadow-xl z-[1100]">

                    <div class="flex items-center justify-between px-4 py-3 border-b border-white/10 sticky top-0 bg-[#111]">
                        <span class="text-[11px] font-bold uppercase tracking-widest text-white">Notificaciones</span>
                        <button @click="marcarTodas()" type="button"
                                class="text-[10px] uppercase tracking-wider text-white/40 hover:text-[#f0c36d] transition-colors">
                            Marcar todas
                        </button>
                    </div>

                    <template x-if="cargando">
                        <p class="px-4 py-6 text-center text-[11px] uppercase tracking-widest text-white/30">Cargando…</p>
                    </template>

                    <template x-if="!cargando && items.length === 0">
                        <p class="px-4 py-6 text-center text-[11px] uppercase tracking-widest text-white/30">No tienes notificaciones.</p>
                    </template>

                    <template x-for="n in items" :key="n.id">
                        <a :href="n.url"
                           class="flex items-start gap-3 px-4 py-3 border-b border-white/5 no-underline hover:bg-white/5 transition-colors"
                           :class="n.leida ? 'opacity-50' : ''">
                            <span class="text-base leading-none mt-0.5" x-text="n.icono"></span>
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] font-bold text-gray-200 truncate" x-text="n.titulo"></p>
                                <p class="text-[11px] text-white/50 truncate mt-0.5" x-text="n.mensaje"></p>
                                <p class="text-[9px] uppercase tracking-wider text-white/25 mt-1" x-text="n.hace"></p>
                            </div>
                            <span x-show="!n.leida" class="w-2 h-2 rounded-full bg-[#f0c36d] shrink-0 mt-1"></span>
                        </a>
                    </template>

                    <a href="{{ route('notificaciones.index') }}"
                       class="block px-4 py-3 text-center text-[10px] uppercase tracking-widest text-white/40 hover:text-[#f0c36d] transition-colors no-underline">
                        Ver todas
                    </a>
                </div>
            </div>

            <a href="{{ route('chat.index') }}" class="text-[#ddd] no-underline text-[12px] uppercase tracking-widest hover:text-[#f0c36d] transition-colors relative">
                Chats
                {{-- Badge de mensajes no leídos (se actualiza en vivo por WebSocket) --}}
                @php
                    $noLeidos = \App\Models\Mensaje::whereHas('conversacion', function($q) {
                        $q->where('comprador_id', Auth::id())->orWhere('vendedor_id', Auth::id());
                    })->where('remitente_id', '!=', Auth::id())->whereNull('leido_at')->count();
                @endphp
                <span id="chat-badge"
                    class="absolute -top-2 -right-2 w-2 h-2 rounded-full bg-yellow-500 {{ $noLeidos > 0 ? '' : 'hidden' }}">
                </span>
            </a>
            <a href="{{ route('perfil') }}" class="text-[#ddd] no-underline text-[12px] uppercase tracking-widest hover:text-[#f0c36d] transition-colors">{{ Auth::user()->name }}</a>
            <a href="{{ route('cart.index') }}" class="text-[#ddd] no-underline text-[12px] uppercase tracking-widest hover:text-[#f0c36d] transition-colors relative">
                Carrito
                @if(session('cart') && count(session('cart')) > 0)
                    <span class="absolute -top-2 -right-3 bg-yellow-500 w-2 h-2 rounded-full"></span>
                @endif
            </a>
        @else
            <a href="{{ route('login') }}" class="text-[#ddd] no-underline text-[12px] uppercase tracking-widest hover:text-[#f0c36d] transition-colors">Login</a>
            <a href="{{ route('register') }}" class="text-[#f0c36d] no-underline text-[12px] uppercase tracking-widest hover:text-[#e0c97a] transition-colors">Registro</a>
        @endauth
    </div>
</nav>