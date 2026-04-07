<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    @vite(['resources/css/app.css'])
</head>
<body>

@include('partials.header')

<main>
    <div class="recuadro perfil">
        <img src="{{ asset('assets/photo/plato6.png') }}" alt="foto perfil">
        <hr>
        <h3>{{ $user->username }}</h3>
        <p>{{ $user->email }}</p>
        <br>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">Cerrar sesión</button>
        </form>
    </div>
</main>

</body>
</html>
