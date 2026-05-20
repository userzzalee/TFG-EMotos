<header class="header">
    <nav class="nav-left">
        <a href="#">Taller</a>
        <a href="#">Merchandising</a>
        <a href="#">Repuestos</a>
    </nav>

    <div class="nav-center">
        <a href="{{ url('/') }}" class="btn-inicio">Alyx</a>
    </div>

    <nav class="nav-right">
        @auth
            <a href="{{ route('perfil') }}">{{ Auth::user()->username }}</a>
            <a href="#">Carrito</a>
        @else
            <a href="{{ route('login') }}">Login</a>
            <a href="{{ route('register') }}">Registro</a>
        @endauth
    </nav>
</header>
