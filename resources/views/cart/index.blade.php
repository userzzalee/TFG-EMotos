<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Carrito - Alyx</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="m-0 bg-black min-h-screen overflow-x-hidden">

@include('layouts.navigation')

<div class="fixed inset-0 bg-gradient-to-br from-gray-900 via-black to-gray-900 z-0 pointer-events-none"></div>

<main class="relative z-10 text-white pt-[60px] min-h-screen">

    @if(empty($cart))
        {{-- Carrito vacío --}}
        <div class="flex items-center justify-center min-h-[calc(100vh-60px)] px-4">
            <div class="text-center">
                <h1 class="text-xl font-semibold mb-3 text-yellow-500">Carrito</h1>

                @if(session('success'))
                    <div class="bg-green-900/50 border border-green-500 text-green-400 px-4 py-2 mb-5 rounded-xl text-sm">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-900/50 border border-red-500 text-red-400 px-4 py-2 mb-5 rounded-xl text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <p class="text-gray-400 text-lg mb-6">Tu carrito está vacío</p>
                <a href="{{ route('merchandising') }}"
                   class="inline-block px-6 py-2 bg-yellow-500 text-black rounded-xl font-medium hover:bg-yellow-400 transition-all text-sm">
                    VER PRODUCTOS
                </a>
            </div>
        </div>

    @else
        {{-- Layout: columna en móvil, fila en escritorio --}}
        <div class="flex flex-col lg:flex-row lg:min-h-[calc(100vh-60px)]">

            {{-- Lista de productos --}}
            <div class="flex-1 p-4 sm:p-5 lg:overflow-y-auto">
                <h1 class="text-xl font-semibold mb-1 text-yellow-500">Carrito</h1>
                <p class="text-gray-400 mb-5 text-sm">{{ count($cart) }} producto{{ count($cart) !== 1 ? 's' : '' }}</p>

                @if(session('success'))
                    <div class="bg-green-900/50 border border-green-500 text-green-400 px-4 py-2 mb-5 rounded-xl text-sm">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-900/50 border border-red-500 text-red-400 px-4 py-2 mb-5 rounded-xl text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="space-y-3 max-w-2xl">
                    @foreach($cart as $item)
                        <div class="bg-gray-800/60 rounded-xl p-3 flex gap-3 items-center border border-gray-700/50 hover:border-yellow-500/50 transition-all">

                            {{-- Imagen --}}
                            <div class="w-14 h-14 sm:w-16 sm:h-16 flex-shrink-0 rounded-lg overflow-hidden">
                                @if(isset($item['categoria']) && $item['categoria'] === 'Configuración')
                                    <img src="{{ asset($item['imagen']) }}" alt="{{ $item['nombre'] }}"
                                         class="w-full h-full object-cover"
                                         onerror="this.src='{{ asset('images/moto1.jpg') }}'">
                                @else
                                    <img src="{{ asset('storage/' . $item['imagen']) }}" alt="{{ $item['nombre'] }}"
                                         class="w-full h-full object-cover"
                                         onerror="this.src='{{ asset('images/moto1.jpg') }}'">
                                @endif
                            </div>

                            {{-- Info --}}
                            <div class="flex-grow min-w-0">
                                <h3 class="font-semibold text-sm truncate">{{ $item['nombre'] }}</h3>
                                <p class="text-yellow-500 text-base font-bold">€{{ number_format($item['precio'], 2) }}</p>
                            </div>

                            {{-- Cantidad --}}
                            <form action="{{ route('cart.update', $item['id']) }}" method="POST" class="flex-shrink-0">
                                @csrf
                                <input type="number" name="cantidad" value="{{ $item['cantidad'] }}" min="1" max="10"
                                       class="w-12 bg-gray-700/80 text-white text-center py-1.5 rounded-lg border border-gray-600 focus:border-yellow-500 focus:outline-none font-medium text-xs">
                            </form>

                            {{-- Subtotal --}}
                            <div class="text-right w-16 sm:w-20 flex-shrink-0">
                                <p class="text-sm sm:text-base font-bold">€{{ number_format($item['precio'] * $item['cantidad'], 2) }}</p>
                            </div>

                            {{-- Eliminar --}}
                            <form action="{{ route('cart.remove', $item['id']) }}" method="POST" class="flex-shrink-0">
                                @csrf
                                <button type="submit"
                                        class="text-gray-500 hover:text-red-500 transition-colors p-1.5 hover:bg-red-500/10 rounded-lg text-sm">
                                    ✕
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Panel resumen --}}
            <div class="w-full lg:w-80 xl:w-96 bg-black/80 backdrop-blur-xl border-t lg:border-t-0 lg:border-l border-gray-800 p-4 sm:p-5 flex flex-col gap-3 lg:pt-12">
                <h2 class="text-lg font-semibold text-yellow-500 text-center">Resumen</h2>
                <p class="text-gray-400 text-center text-sm mb-1">Revisa tu pedido</p>

                <div class="space-y-2">
                    <div class="bg-gray-800/80 rounded-lg p-3 border border-gray-700/50">
                        <p class="text-gray-400 text-xs mb-0.5">Subtotal</p>
                        <p class="text-base font-semibold">€{{ number_format($total, 2) }}</p>
                    </div>
                    <div class="bg-gray-800/80 rounded-lg p-3 border border-gray-700/50">
                        <p class="text-gray-400 text-xs mb-0.5">Envío</p>
                        <p class="text-sm font-semibold text-green-400">GRATIS</p>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-xl p-3">
                    <p class="text-gray-400 text-xs">Total</p>
                    <p class="text-xl font-semibold text-yellow-500">€{{ number_format($total, 2) }}</p>
                </div>

                <a href="{{ route('order.checkout') }}"
                   class="w-full bg-yellow-500 text-black rounded-xl py-3 font-medium hover:bg-yellow-400 active:scale-[0.98] transition-all text-sm text-center block">
                    PROCEDER AL PAGO
                </a>

                <div class="flex gap-3">
                    <a href="{{ route('merchandising') }}"
                       class="flex-1 text-center bg-gray-800 text-white rounded-xl py-2 hover:bg-gray-700 transition-all text-sm">
                        Seguir comprando
                    </a>
                    <form action="{{ route('cart.clear') }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit"
                                class="w-full text-center bg-gray-800 text-red-400 rounded-xl py-2 hover:bg-gray-700 transition-all text-sm">
                            Vaciar carrito
                        </button>
                    </form>
                </div>
            </div>

        </div>
    @endif

</main>

</body>
</html>
