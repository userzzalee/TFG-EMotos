<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Carrito - Alyx</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="m-0 bg-black h-screen overflow-x-hidden">

@include('layouts.navigation')

<div class="fixed inset-0 bg-gradient-to-br from-gray-900 via-black to-gray-900 z-0 pointer-events-none"></div>

<main class="relative z-10 text-white h-screen flex pt-[70px]">
    
    <!-- Panel izquierdo - Lista de productos -->
    <div class="w-2/3 h-full p-8 overflow-y-auto">
        <h1 class="text-3xl font-semibold mb-2 text-yellow-500">Carrito</h1>
        <p class="text-gray-400 mb-8">{{ count($cart) }} producto{{ count($cart) !== 1 ? 's' : '' }}</p>

        @if(session('success'))
            <div class="bg-green-900/50 border border-green-500 text-green-400 px-6 py-4 mb-8 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-900/50 border border-red-500 text-red-400 px-6 py-4 mb-8 rounded-xl">
                {{ session('error') }}
            </div>
        @endif

        @if(empty($cart))
            <div class="text-center py-20">
                <p class="text-gray-400 text-xl mb-8">Tu carrito está vacío</p>
                <a href="{{ route('merchandising') }}" class="inline-block px-8 py-3 bg-yellow-500 text-black rounded-xl font-medium hover:bg-yellow-400 transition-all">
                    VER PRODUCTOS
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($cart as $item)
                    <div class="bg-gray-800/60 rounded-2xl p-4 flex gap-4 items-center border border-gray-700/50 hover:border-yellow-500/50 transition-all">
                        <!-- Imagen -->
                        <div class="w-20 h-20 flex-shrink-0 rounded-xl overflow-hidden shadow-lg">
                            <img src="{{ asset('storage/' . $item['imagen']) }}" alt="{{ $item['nombre'] }}" class="w-full h-full object-cover">
                        </div>
                        
                        <!-- Info -->
                        <div class="flex-grow">
                            <h3 class="font-semibold text-base">{{ $item['nombre'] }}</h3>
                            <p class="text-yellow-500 text-lg font-bold">€{{ number_format($item['precio'], 2) }}</p>
                        </div>
                        
                        <!-- Cantidad -->
                        <div class="flex items-center">
                            <form action="{{ route('cart.update', $item['id']) }}" method="POST">
                                @csrf
                                <input type="number" name="cantidad" value="{{ $item['cantidad'] }}" min="1" max="10" class="w-14 bg-gray-700/80 text-white text-center py-2 rounded-lg border border-gray-600 focus:border-yellow-500 focus:outline-none font-medium text-sm">
                            </form>
                        </div>
                        
                        <!-- Subtotal -->
                        <div class="text-right w-20">
                            <p class="text-lg font-bold">€{{ number_format($item['precio'] * $item['cantidad'], 2) }}</p>
                        </div>
                        
                        <!-- Eliminar -->
                        <form action="{{ route('cart.remove', $item['id']) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-gray-500 hover:text-red-500 transition-colors p-2 hover:bg-red-500/10 rounded-xl">
                                ✕
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    
    <!-- Panel derecho - Resumen -->
    @if(!empty($cart))
        <div class="w-1/3 h-full bg-black/80 backdrop-blur-xl border-l border-gray-800 p-8 overflow-y-auto flex flex-col justify-start pt-16">
            <h2 class="text-2xl font-semibold mb-2 text-yellow-500 text-center">Resumen</h2>
            <p class="text-gray-400 mb-8 text-center">Revisa tu pedido</p>
            
            <div class="space-y-3 mb-8">
                <div class="bg-gray-800/80 rounded-lg p-4 border border-gray-700/50 hover:border-yellow-500/50 transition-colors">
                    <p class="text-gray-400 text-sm mb-1">Subtotal</p>
                    <p class="text-xl font-semibold">€{{ number_format($total, 2) }}</p>
                </div>
                <div class="bg-gray-800/80 rounded-lg p-4 border border-gray-700/50 hover:border-green-500/50 transition-colors">
                    <p class="text-gray-400 text-sm mb-1">Envío</p>
                    <p class="text-lg font-semibold text-green-400">GRATIS</p>
                </div>
            </div>
            
            <div class="bg-gray-800 rounded-xl p-4 mb-8">
                <p class="text-gray-400 text-sm">Total</p>
                <p class="text-3xl font-semibold text-yellow-500">€{{ number_format($total, 2) }}</p>
            </div>
            
            <button class="w-full bg-yellow-500 text-black rounded-xl py-4 font-medium hover:bg-yellow-400 transition-all mb-4">
                PROCEDER AL PAGO
            </button>
            
            <div class="flex gap-4">
                <a href="{{ route('merchandising') }}" class="flex-1 text-center bg-gray-800 text-white rounded-xl py-3 hover:bg-gray-700 transition-all">
                    Seguir comprando
                </a>
                <form action="{{ route('cart.clear') }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full text-center bg-gray-800 text-red-400 rounded-xl py-3 hover:bg-gray-700 transition-all">
                        Vaciar carrito
                    </button>
                </form>
            </div>
        </div>
    @endif
    
</main>

</body>
</html>
