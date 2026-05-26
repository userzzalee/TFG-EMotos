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

<main class="relative z-10 text-white pt-[100px] pb-20">

    <!-- Filtros -->
    <div class="max-w-7xl mx-auto px-8 mb-12">
        <div class="flex gap-6 justify-center flex-wrap">
            <button class="boton-filtro px-6 py-2 border border-gray-700 text-sm tracking-widest hover:border-yellow-500 hover:text-yellow-500 transition-all" data-filter="all">TODOS</button>
            <button class="boton-filtro px-6 py-2 border border-gray-700 text-sm tracking-widest hover:border-yellow-500 hover:text-yellow-500 transition-all" data-filter="ropa">ROPA</button>
            @auth
                @if(Auth::user()->esAdmin())
                    <a href="{{ route('merchandising.create') }}" class="px-8 py-3 border border-yellow-500 text-yellow-500 text-base tracking-widest hover:bg-yellow-500 hover:text-black transition-all">CREAR PUBLICACIÓN</a>
                @endif
            @endauth
            <button class="boton-filtro px-6 py-2 border border-gray-700 text-sm tracking-widest hover:border-yellow-500 hover:text-yellow-500 transition-all" data-filter="accesorios">ACCESORIOS</button>
            <button class="boton-filtro px-6 py-2 border border-gray-700 text-sm tracking-widest hover:border-yellow-500 hover:text-yellow-500 transition-all" data-filter="cascos">CASCOS</button>
        </div>
    </div>

    <!-- Grid de Productos -->
    <div class="max-w-7xl mx-auto px-8">
        @if($productos->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($productos as $producto)
                    <div class="tarjeta-producto group cursor-pointer" data-category="{{ $producto->categoria ?? 'all' }}">
                        <div class="relative overflow-hidden bg-gray-900 aspect-[3/4] mb-4">
                            <img src="{{ asset('storage/' . $producto->imagen) }}" 
                                 alt="{{ $producto->nombre }}" 
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all duration-300"></div>
                            
                            <!-- Botón añadir al carrito (hover) -->
                            <div class="absolute bottom-0 left-0 right-0 p-6 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                                    <input type="hidden" name="cantidad" value="1">
                                    <button type="submit" class="w-full bg-white text-black py-3 text-sm tracking-widest font-medium hover:bg-yellow-500 transition-all">
                                        AÑADIR
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <h3 class="text-lg font-light tracking-wider mb-2">{{ $producto->nombre }}</h3>
                            <p class="text-yellow-500 text-xl font-light">€{{ number_format($producto->precio, 2) }}</p>
                            @if($producto->categoria)
                                <p class="text-gray-500 text-xs tracking-widest mt-2 uppercase">{{ $producto->categoria }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-20">
                <p class="text-gray-400 text-xl tracking-wider">No hay productos disponibles</p>
            </div>
        @endif
    </div>

</main>

</body>
</html>
