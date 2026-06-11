<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

@include('layouts.navigation')

<div class="min-h-screen bg-black flex flex-col items-center justify-center pt-[60px]">

    <div class="bg-[rgb(30,30,30)]/90 text-center p-4 w-[35%] text-white mt-0 rounded-lg border border-gray-700/50">

        <h1 class="text-lg font-bold mb-2 uppercase tracking-widest">Mi Perfil</h1>
        <hr class="border-gray-700 mb-4">

        <div class="flex flex-col items-center gap-3 mb-4">
            <div class="w-16 h-16 bg-[#c9b37e]/20 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-[#c9b37e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold">{{ Auth::user()->name }} {{ Auth::user()->apellido ?? '' }}</h2>
            </div>
        </div>

        <div class="text-left space-y-2 mb-4">
            <div class="bg-black p-3 rounded-lg">
                <p class="text-gray-500 text-[10px] uppercase tracking-widest mb-0.5">Correo electrónico</p>
                <p class="text-white text-sm">{{ Auth::user()->email }}</p>
            </div>
            @if(Auth::user()->telefono)
            <div class="bg-black p-3 rounded-lg">
                <p class="text-gray-500 text-[10px] uppercase tracking-widest mb-0.5">Teléfono</p>
                <p class="text-white text-sm">{{ Auth::user()->telefono }}</p>
            </div>
            @endif
            <div class="bg-black p-3 rounded-lg">
                <p class="text-gray-500 text-[10px] uppercase tracking-widest mb-0.5">Rol</p>
                <p class="text-white capitalize text-sm">{{ Auth::user()->rol ?? 'usuario' }}</p>
            </div>
        </div>

        <div class="flex gap-3 justify-center">
            <a href="{{ route('order.index') }}"
               class="w-[120px] h-[32px] text-white bg-gray-700 hover:bg-gray-600 font-medium rounded-[20px] cursor-pointer border-none transition-colors text-xs flex items-center justify-center">
                Mis Pedidos
            </a>
            @if(Auth::user()->esAdmin())
                <a href="{{ url('/admin/usuarios') }}"
                   class="w-[120px] h-[32px] text-white bg-gray-700 hover:bg-gray-600 font-medium rounded-[20px] cursor-pointer border-none transition-colors text-xs flex items-center justify-center">
                    Panel Admin
                </a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-[120px] h-[32px] text-white bg-[#c9b37e] hover:bg-[#e0c97a] text-[#1e1f29] font-medium rounded-[20px] cursor-pointer border-none transition-colors text-xs">
                    Cerrar sesión
                </button>
            </form>
        </div>

    </div>

    <div class="mt-3">
        <a href="{{ url('/') }}"
            class="text-gray-400 no-underline hover:text-white transition-colors text-xs">
            Volver al inicio
        </a>
    </div>

</div>

</body>
</html>
