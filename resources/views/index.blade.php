<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Alyx</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="m-0 bg-black min-h-screen overflow-x-hidden">

@include('layouts.navigation')

<video autoplay muted loop playsinline
        class="fixed inset-0 w-full h-full object-cover -z-10 pointer-events-none">
    <source src="{{ asset('videos/fondo.mp4') }}" type="video/mp4">
</video>

<div class="fixed inset-0 bg-black/40 z-0 pointer-events-none"></div>

<main class="relative z-10 text-white text-center">
    <div class="flex items-center justify-center min-h-screen px-4">
        <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold tracking-widest leading-tight">
            RIDE ANYWHERE, <br> ANYTIME
        </h2>
    </div>

    <div class="fixed bottom-8 sm:bottom-10 left-0 w-full flex justify-center gap-3 px-4">
        <a href="{{ route('configurador') }}"
           class="bg-[rgb(224,224,224)] rounded-[25px] px-6 py-3 w-full max-w-[180px] sm:w-[140px] sm:py-[10px]
                  border-none cursor-pointer transition-all duration-200 hover:scale-105 hover:bg-[rgb(210,210,210)]
                  no-underline text-black block text-center text-sm">
           Configura tu moto
        </a>
    </div>
</main>

</body>
</html>
