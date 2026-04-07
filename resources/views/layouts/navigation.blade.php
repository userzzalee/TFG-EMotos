<nav x-data="{ open: false }"
    class="fixed top-0 left-0 w-full h-[70px] z-[1000] grid grid-cols-[1fr_auto_1fr] items-center px-[50px]
            {{ request()->is('/') ? 'bg-gradient-to-b from-black/100 via-black/70 to-black/0' : 'bg-black' }}">

    {{-- Nav izquierda --}}
    <div class="flex items-center gap-5">
        <a href="#" class="text-[#ddd] no-underline text-[14px] uppercase tracking-widest hover:text-[#f0c36d] transition-colors">Supermotard</a>
        <a href="#" class="text-[#ddd] no-underline text-[14px] uppercase tracking-widest hover:text-[#f0c36d] transition-colors">Motocross</a>
        <a href="#" class="text-[#ddd] no-underline text-[14px] uppercase tracking-widest hover:text-[#f0c36d] transition-colors">Enduro</a>
    </div>

    {{-- Centro: logo --}}
    <div class="justify-self-center">
        <a href="{{ url('/') }}" class="text-white font-bold text-[16px] no-underline tracking-widest">CLUB MOTOS</a>
    </div>

    {{-- Nav derecha --}}
    <div class="justify-self-end flex items-center gap-5">
        @auth
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="text-[#ddd] bg-transparent border-none cursor-pointer text-[14px] uppercase tracking-widest hover:text-[#f0c36d] transition-colors">
                        {{ Auth::user()->username }}
                    </button>
                </x-slot>
                <x-slot name="content">
                    <x-dropdown-link :href="route('perfil')">Mi perfil</x-dropdown-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                            Cerrar sesión
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        @else
            <a href="{{ route('login') }}"    class="text-[#ddd] no-underline text-[14px] uppercase tracking-widest hover:text-[#f0c36d] transition-colors">Login</a>
            <a href="{{ route('register') }}" class="text-[#f0c36d] no-underline text-[14px] uppercase tracking-widest hover:text-[#e0c97a] transition-colors">Registro</a>
        @endauth
    </div>
</nav>
