<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Merchandising - Alyx</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('js/merchandising.js') }}" defer></script>
</head>
<body class="m-0 bg-black min-h-screen overflow-x-hidden">

@include('layouts.navigation')

<div class="fixed inset-0 bg-gradient-to-br from-gray-900 via-black to-gray-900 z-0 pointer-events-none"></div>

<main class="relative z-10 text-white pt-[75px] pb-16">

    {{-- Filtros --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 mb-8">
        <div class="flex flex-col gap-4 items-center">

            {{-- Botones de filtro rápidos --}}
            <div class="flex gap-2 flex-wrap justify-center">
                <button class="boton-filtro px-3 sm:px-4 py-1.5 border border-gray-700 text-xs tracking-widest hover:border-yellow-500 hover:text-yellow-500 transition-all" data-filter="all">TODOS</button>
                <button class="boton-filtro px-3 sm:px-4 py-1.5 border border-gray-700 text-xs tracking-widest hover:border-yellow-500 hover:text-yellow-500 transition-all" data-filter="ropa">ROPA</button>
                <button class="boton-filtro px-3 sm:px-4 py-1.5 border border-gray-700 text-xs tracking-widest hover:border-yellow-500 hover:text-yellow-500 transition-all" data-filter="accesorios">ACCESORIOS</button>
                <button class="boton-filtro px-3 sm:px-4 py-1.5 border border-gray-700 text-xs tracking-widest hover:border-yellow-500 hover:text-yellow-500 transition-all" data-filter="cascos">CASCOS</button>
                @auth
                    @if(Auth::user()->esAdmin())
                        <a href="{{ route('merchandising.create') }}"
                           class="px-4 sm:px-5 py-2 border border-yellow-500 text-yellow-500 text-xs tracking-widest hover:bg-yellow-500 hover:text-black transition-all">CREAR</a>
                    @endif
                @endauth
            </div>

            {{-- Buscador y select --}}
            <form method="GET" action="{{ route('merchandising') }}"
                  class="flex flex-wrap gap-2 sm:gap-3 items-center justify-center w-full sm:w-auto">
                <input type="text" name="buscar" value="{{ request('buscar') }}"
                       placeholder="Buscar productos…"
                       class="bg-gray-900 border border-gray-700 text-white text-xs px-4 py-2 rounded focus:border-yellow-500 focus:outline-none transition-all w-full sm:w-56 placeholder-gray-600">
                <div class="flex gap-2 w-full sm:w-auto">
                    <input type="number" name="precio_min" value="{{ request('precio_min') }}"
                           placeholder="Mín €" min="0" step="0.01"
                           class="bg-gray-900 border border-gray-700 text-white text-xs px-3 py-2 rounded focus:border-yellow-500 focus:outline-none transition-all flex-1 sm:w-24 placeholder-gray-600">
                    <input type="number" name="precio_max" value="{{ request('precio_max') }}"
                           placeholder="Máx €" min="0" step="0.01"
                           class="bg-gray-900 border border-gray-700 text-white text-xs px-3 py-2 rounded focus:border-yellow-500 focus:outline-none transition-all flex-1 sm:w-24 placeholder-gray-600">
                    <button type="submit"
                            class="px-4 sm:px-5 py-2 border border-yellow-500 text-yellow-500 text-xs tracking-widest hover:bg-yellow-500 hover:text-black transition-all whitespace-nowrap">
                        Filtrar
                    </button>
                </div>
            </form>

            @if(request()->hasAny(['buscar','precio_min','precio_max']))
                <a href="{{ route('merchandising') }}"
                   class="text-xs text-gray-500 hover:text-white transition-colors mt-1">
                   Limpiar filtros
                </a>
            @endif
        </div>
    </div>

    {{-- Grid de productos --}}
    <div class="max-w-5xl mx-auto px-4 sm:px-6">
        @if($productos->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4">
                @foreach($productos as $producto)
                    <div class="tarjeta-producto group cursor-pointer" data-category="{{ $producto->categoria ?? 'all' }}">
                        <div class="relative overflow-hidden bg-gray-900 aspect-[3/4] mb-3">
                            <img src="{{ asset('storage/' . $producto->imagen) }}"
                                 alt="{{ $producto->nombre }}"
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all duration-300"></div>

                            {{-- Botón editar (solo admin) --}}
                            @auth
                                @if(Auth::user()->esAdmin())
                                    <a href="{{ route('merchandising.edit', $producto->id) }}"
                                       class="absolute top-2 right-2 bg-yellow-500 text-black px-2 py-1 rounded text-xs font-medium hover:bg-yellow-400 transition-all z-10">
                                        Editar
                                    </a>
                                @endif
                            @endauth

                            {{-- Añadir al carrito: hover en escritorio, siempre visible en móvil --}}
                            <div class="absolute bottom-0 left-0 right-0 p-3
                                        sm:translate-y-full sm:group-hover:translate-y-0 sm:transition-transform sm:duration-300">
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                                    <input type="hidden" name="cantidad" value="1">
                                    <button type="submit"
                                            class="w-full bg-white text-black py-2 text-xs tracking-widest font-medium hover:bg-yellow-500 active:scale-[0.98] transition-all"
                                            onclick="this.disabled=true;this.form.submit();">
                                        AÑADIR
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="text-center px-1">
                            <h3 class="text-xs sm:text-sm font-light tracking-wider mb-1 truncate">{{ $producto->nombre }}</h3>
                            <div class="estrellas flex justify-center gap-0.5 mb-1 select-none" data-producto-id="{{ $producto->id }}"></div>
                            <p class="text-yellow-500 text-sm sm:text-base font-light">€{{ number_format($producto->precio, 2) }}</p>
                            @if($producto->categoria)
                                <p class="text-gray-500 text-[10px] tracking-widest mt-1 uppercase">{{ $producto->categoria }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="grid place-items-center min-h-[400px]">
                <p class="text-gray-400 text-lg tracking-wider">No hay productos disponibles</p>
            </div>
        @endif
    </div>

</main>

</body>
</html>
