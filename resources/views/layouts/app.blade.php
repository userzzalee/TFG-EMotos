<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Club Motos</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="margin:0; background-color: black;">

@include('layouts.navigation')

<video autoplay muted loop playsinline id="bg-video">
    <source src="{{ asset('videos/fondo.mp4') }}" type="video/mp4">
</video>

<div class="overlay"></div>

<main class="index-main">
    <h2>RIDE ANYWHERE, <br> ANYTIME</h2>
    <br>
    <button type="button"><a href="#">Supermotard</a></button>
    <button type="button"><a href="#">Motocross</a></button>
    <button type="button"><a href="#">Enduro</a></button>
</main>

</body>
</html>
