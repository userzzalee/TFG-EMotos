<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    @vite(['resources/css/app.css'])
</head>
<body>

@include('layouts.navigation')

<div class="min-h-screen bg-black flex flex-col items-center justify-start pt-[70px]">

    <div class="bg-[rgb(17,17,17)]/70 text-center p-6 w-[40%] text-white mt-[50px] rounded-lg">

        <h1 class="text-xl font-bold mb-3 uppercase tracking-widest">Mi Perfil</h1>
        <hr class="border-gray-700 mb-6">

        <div class="flex flex-col items-center gap-4 mb-6">
            <div class="w-24 h-24 bg-[#c9b37e]/20 rounded-full flex items-center justify-center">
                <svg class="w-12 h-12 text-[#c9b37e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-bold">{{ Auth::user()->name }} {{ Auth::user()->apellido ?? '' }}</h2>
                <p class="text-gray-400 text-sm">@{{ Auth::user()->username }}</p>
            </div>
        </div>

        <div class="text-left space-y-3 mb-6">
            <div class="bg-black p-4 rounded-lg">
                <p class="text-gray-500 text-xs uppercase tracking-widest mb-1">Correo electrónico</p>
                <p class="text-white">{{ Auth::user()->email }}</p>
            </div>
            @if(Auth::user()->telefono)
            <div class="bg-black p-4 rounded-lg">
                <p class="text-gray-500 text-xs uppercase tracking-widest mb-1">Teléfono</p>
                <p class="text-white">{{ Auth::user()->telefono }}</p>
            </div>
            @endif
            <div class="bg-black p-4 rounded-lg">
                <p class="text-gray-500 text-xs uppercase tracking-widest mb-1">Rol</p>
                <p class="text-white capitalize">{{ Auth::user()->rol ?? 'usuario' }}</p>
            </div>
        </div>

        <div class="flex gap-3 justify-center">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-[150px] h-[40px] text-white bg-[#c9b37e] hover:bg-[#e0c97a] text-[#1e1f29] font-medium rounded-[30px] cursor-pointer border-none transition-colors">
                    Cerrar sesión
                </button>
            </form>
        </div>

    </div>

    <div class="mt-4">
        <a href="{{ url('/') }}"
            class="text-gray-400 no-underline hover:text-white transition-colors">
            Volver al inicio
        </a>
    </div>

</div>

</body>
</html>
