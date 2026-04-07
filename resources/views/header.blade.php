<header class="header">
    <nav class="nav-left">
        <a href="#">Supermotard</a>
        <a href="#">Motocross</a>
        <a href="#">Enduro</a>
    </nav>

    <div class="nav-center">
        <a href="{{ url('/') }}" class="home-btn">CLUB MOTOS</a>
    </div>

    <nav class="nav-right">
        @auth
            <a href="{{ route('perfil') }}">{{ Auth::user()->username }}</a>
        @else
            <a href="{{ route('login') }}">Login</a>
            <a href="{{ route('register') }}">Registro</a>
        @endauth
    </nav>
</header>
