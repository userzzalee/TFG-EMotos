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
        <a href="#" class="text-[#ddd] no-underline text-[12px] uppercase tracking-widest hover:text-[#f0c36d] transition-colors">Repuestos</a>
    </div>

    {{-- Logo --}}
    <div class="justify-self-center">
        <a href="{{ url('/') }}" class="text-white font-bold text-[22px] no-underline tracking-widest">Aly<span class="text-[#f0c36d]">X</span></a>
    </div>

    {{-- Controlores derecha --}}
    <div class="justify-self-end flex items-center gap-4">
        @auth
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
