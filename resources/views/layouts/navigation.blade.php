<style>
    #nav-desktop-links { display: flex; }
    #nav-desktop-right  { display: flex; }
    #nav-hamburger      { display: none; }
    #nav-mobile-menu    { display: none; }

    @media (max-width: 767px) {
        #nav-desktop-links { display: none !important; }
        #nav-desktop-right  { display: none !important; }
        #nav-hamburger      { display: flex; }
    }
</style>

<nav x-data="{ open: false }"
     class="fixed top-0 left-0 w-full h-[60px] z-[1000] flex items-center justify-between px-4"
     style="background: {{ request()->is('/') ? 'linear-gradient(to bottom, rgba(0,0,0,1), rgba(0,0,0,0.7), rgba(0,0,0,0))' : 'black' }};">

    {{-- Logo --}}
    <a href="{{ url('/') }}"
       style="color:white; font-weight:700; font-size:20px; text-decoration:none; letter-spacing:.15em; flex-shrink:0;">
        Aly<span style="color:#f0c36d;">X</span>
    </a>

    {{-- Links centro (escritorio) --}}
    <div id="nav-desktop-links"
         style="position:absolute; left:50%; transform:translateX(-50%); align-items:center; gap:20px;">
        @auth
            @if(Auth::user()->rol === 'mecanico' || Auth::user()->esAdmin())
                <a href="{{ route('taller.nuevas-citas') }}" class="nav-link">Taller</a>
            @else
                <a href="{{ route('taller.crear') }}" class="nav-link">Taller</a>
            @endif
        @else
            <a href="{{ route('login') }}" class="nav-link">Taller</a>
        @endauth
        <a href="{{ route('merchandising') }}" class="nav-link">Merchandising</a>
        <a href="{{ route('segundamano.index') }}" class="nav-link">Segunda Mano</a>
        @auth
            @if(Auth::user()->esAdmin())
                <a href="{{ route('admin.dashboard') }}" class="nav-link" style="color:#f0c36d;">Admin</a>
            @endif
        @endauth
    </div>

    {{-- Derecha (escritorio) --}}
    <div id="nav-desktop-right" style="align-items:center; gap:16px; flex-shrink:0;">
        @auth
            {{-- Campana --}}
            @php $notiNoLeidas = Auth::user()->unreadNotifications()->count(); @endphp
            <div x-data="notificaciones({ noLeidas: {{ $notiNoLeidas }}, userId: {{ Auth::id() }}, open: false })"
                 x-init="init()" style="position:relative; display:flex; align-items:center;">
                <button @click="toggle()" type="button"
                        style="position:relative; color:#ddd; background:none; border:none; cursor:pointer; display:flex; align-items:center;"
                        aria-label="Notificaciones">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:18px;height:18px;" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                    </svg>
                    <span x-show="noLeidas > 0" x-cloak x-text="noLeidas > 99 ? '99+' : noLeidas"
                          style="position:absolute;top:-8px;right:-12px;min-width:16px;height:16px;padding:0 3px;border-radius:9999px;background:#eab308;color:black;font-size:10px;font-weight:700;line-height:16px;text-align:center;"></span>
                </button>

                <div x-show="open" x-cloak @click.outside="open = false"
                     style="position:absolute;top:100%;right:0;margin-top:12px;width:320px;max-height:420px;overflow-y:auto;border-radius:10px;border:1px solid rgba(255,255,255,.1);background:#111;z-index:1100;">
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid rgba(255,255,255,.1);position:sticky;top:0;background:#111;">
                        <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:white;">Notificaciones</span>
                        <button @click="marcarTodas()" type="button" style="font-size:10px;text-transform:uppercase;letter-spacing:.05em;color:rgba(255,255,255,.4);background:none;border:none;cursor:pointer;">Marcar todas</button>
                    </div>
                    <template x-if="cargando">
                        <p style="padding:24px 16px;text-align:center;font-size:11px;text-transform:uppercase;color:rgba(255,255,255,.3);">Cargando…</p>
                    </template>
                    <template x-if="!cargando && items.length === 0">
                        <p style="padding:24px 16px;text-align:center;font-size:11px;text-transform:uppercase;color:rgba(255,255,255,.3);">No tienes notificaciones.</p>
                    </template>
                    <template x-for="n in items" :key="n.id">
                        <a :href="n.url" style="display:flex;align-items:flex-start;gap:12px;padding:12px 16px;border-bottom:1px solid rgba(255,255,255,.05);text-decoration:none;" :style="n.leida ? 'opacity:.5' : ''">
                            <span style="font-size:16px;line-height:1;margin-top:2px;" x-text="n.icono"></span>
                            <div style="min-width:0;flex:1;">
                                <p style="font-size:11px;font-weight:700;color:#e5e7eb;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" x-text="n.titulo"></p>
                                <p style="font-size:11px;color:rgba(255,255,255,.5);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-top:2px;" x-text="n.mensaje"></p>
                                <p style="font-size:9px;text-transform:uppercase;color:rgba(255,255,255,.25);margin-top:4px;" x-text="n.hace"></p>
                            </div>
                            <span x-show="!n.leida" style="width:8px;height:8px;border-radius:9999px;background:#f0c36d;flex-shrink:0;margin-top:4px;"></span>
                        </a>
                    </template>
                    <a href="{{ route('notificaciones.index') }}"
                       style="display:block;padding:12px 16px;text-align:center;font-size:10px;text-transform:uppercase;letter-spacing:.08em;color:rgba(255,255,255,.4);text-decoration:none;">Ver todas</a>
                </div>
            </div>

            {{-- Chat --}}
            @php
                $noLeidos = \App\Models\Mensaje::whereHas('conversacion', function($q) {
                    $q->where('comprador_id', Auth::id())->orWhere('vendedor_id', Auth::id());
                })->where('remitente_id', '!=', Auth::id())->whereNull('leido_at')->count();
            @endphp
            <a href="{{ route('chat.index') }}" class="nav-link" style="position:relative;">
                Chats
                <span id="chat-badge" style="position:absolute;top:-8px;right:-8px;width:8px;height:8px;border-radius:9999px;background:#eab308;{{ $noLeidos > 0 ? '' : 'display:none;' }}"></span>
            </a>

            <a href="{{ route('perfil') }}" class="nav-link" style="max-width:80px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ Auth::user()->name }}</a>

            <a href="{{ route('cart.index') }}" class="nav-link" style="position:relative;">
                Carrito
                @if(session('cart') && count(session('cart')) > 0)
                    <span style="position:absolute;top:-8px;right:-10px;width:8px;height:8px;border-radius:9999px;background:#eab308;"></span>
                @endif
            </a>
        @else
            <a href="{{ route('login') }}" class="nav-link">Login</a>
            <a href="{{ route('register') }}" class="nav-link" style="color:#f0c36d;">Registro</a>
        @endauth
    </div>

    {{-- Hamburguesa --}}
    <button id="nav-hamburger" @click="open = !open" type="button"
            style="flex-direction:row;align-items:center;gap:8px;padding:6px 10px;background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.15);border-radius:8px;cursor:pointer;flex-shrink:0;transition:background .2s,border-color .2s;"
            onmouseover="this.style.background='rgba(240,195,109,0.12)';this.style.borderColor='rgba(240,195,109,0.4)'"
            onmouseout="this.style.background='rgba(255,255,255,0.07)';this.style.borderColor='rgba(255,255,255,0.15)'"
            aria-label="Menú">
        <div style="display:flex;flex-direction:column;justify-content:center;gap:4px;">
            <span style="display:block;width:18px;height:1.5px;background:white;transition:all .2s;border-radius:2px;"
                  :style="open ? 'transform:rotate(45deg) translateY(5.5px)' : ''"></span>
            <span style="display:block;width:18px;height:1.5px;background:white;transition:all .2s;border-radius:2px;"
                  :style="open ? 'opacity:0' : ''"></span>
            <span style="display:block;width:18px;height:1.5px;background:white;transition:all .2s;border-radius:2px;"
                  :style="open ? 'transform:rotate(-45deg) translateY(-5.5px)' : ''"></span>
        </div>
        <span style="font-size:11px;text-transform:uppercase;letter-spacing:.1em;color:white;" x-text="open ? 'Cerrar' : 'Menú'"></span>
    </button>

    {{-- Menú móvil --}}
    <div x-show="open" x-cloak @click.outside="open = false"
         style="position:absolute;top:60px;left:0;width:100%;background:black;border-bottom:1px solid rgba(255,255,255,.1);z-index:1050;flex-direction:column;padding:16px;gap:16px;"
         :style="open ? 'display:flex' : 'display:none'">

        @auth
            @if(Auth::user()->rol === 'mecanico' || Auth::user()->esAdmin())
                <a href="{{ route('taller.nuevas-citas') }}" @click="open=false" class="nav-link-mobile">Taller</a>
            @else
                <a href="{{ route('taller.crear') }}" @click="open=false" class="nav-link-mobile">Taller</a>
            @endif
        @else
            <a href="{{ route('login') }}" @click="open=false" class="nav-link-mobile">Taller</a>
        @endauth

        <a href="{{ route('merchandising') }}" @click="open=false" class="nav-link-mobile">Merchandising</a>
        <a href="{{ route('segundamano.index') }}" @click="open=false" class="nav-link-mobile">Segunda Mano</a>

        @auth
            @if(Auth::user()->esAdmin())
                <a href="{{ route('admin.dashboard') }}" @click="open=false" class="nav-link-mobile" style="color:#f0c36d;">Admin</a>
            @endif
            <div style="height:1px;background:rgba(255,255,255,.1);"></div>
            <a href="{{ route('chat.index') }}" @click="open=false" class="nav-link-mobile">
                Chats @if($noLeidos > 0) <span style="display:inline-block;width:8px;height:8px;border-radius:9999px;background:#eab308;margin-left:4px;"></span>@endif
            </a>
            <a href="{{ route('perfil') }}" @click="open=false" class="nav-link-mobile">{{ Auth::user()->name }}</a>
            <a href="{{ route('cart.index') }}" @click="open=false" class="nav-link-mobile">
                Carrito @if(session('cart') && count(session('cart')) > 0)<span style="display:inline-block;width:8px;height:8px;border-radius:9999px;background:#eab308;margin-left:4px;"></span>@endif
            </a>
            <a href="{{ route('notificaciones.index') }}" @click="open=false" class="nav-link-mobile">
                Notificaciones @if($notiNoLeidas > 0)<span style="display:inline-block;padding:1px 5px;border-radius:9999px;background:#eab308;color:black;font-size:10px;font-weight:700;margin-left:4px;">{{ $notiNoLeidas }}</span>@endif
            </a>
        @else
            <div style="height:1px;background:rgba(255,255,255,.1);"></div>
            <a href="{{ route('login') }}" @click="open=false" class="nav-link-mobile">Login</a>
            <a href="{{ route('register') }}" @click="open=false" class="nav-link-mobile" style="color:#f0c36d;">Registro</a>
        @endauth
    </div>
</nav>

<style>
    .nav-link {
        color: #ddd;
        text-decoration: none;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .1em;
        transition: color .15s;
    }
    .nav-link:hover { color: #f0c36d; }
    .nav-link-mobile {
        color: #ddd;
        text-decoration: none;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .1em;
        transition: color .15s;
    }
    .nav-link-mobile:hover { color: #f0c36d; }
</style>
