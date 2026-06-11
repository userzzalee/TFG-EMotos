@extends('layouts.general')

@section('content')

<div class="fixed inset-0 bg-gradient-to-br from-gray-900 via-black to-gray-900 z-0 pointer-events-none"></div>

<main class="relative z-10 text-white pt-[75px] pb-20">
    <div class="max-w-4xl mx-auto px-8">

        <p class="text-[10px] tracking-[0.3em] text-yellow-500 uppercase mb-2 text-center">Carrito</p>
        <h1 class="text-2xl font-light tracking-[0.2em] mb-6 text-center uppercase">Finalizar Compra</h1>

        @if(session('error'))
            <div class="bg-red-900/50 border border-red-500 text-red-400 px-6 py-4 rounded-lg mb-8 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('order.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Resumen del pedido -->
            <div class="bg-gray-900/60 rounded-xl p-6 border border-gray-800">
                <h2 class="text-lg font-semibold mb-4 text-yellow-500">Resumen del Pedido</h2>
                <div class="space-y-3">
                    @foreach($cart as $item)
                        <div class="flex justify-between items-center text-sm">
                            <div>
                                <p class="font-medium">{{ $item['nombre'] }}</p>
                                @if(isset($item['categoria']) && $item['categoria'] === 'Configuración')
                                    <p class="text-gray-400 text-xs">Configuración personalizada</p>
                                @endif
                            </div>
                            <p class="text-gray-300">€{{ number_format($item['precio'], 2) }} x {{ $item['cantidad'] }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 pt-4 border-t border-gray-700 flex justify-between items-center">
                    <span class="text-lg font-semibold">Total</span>
                    <span class="text-xl font-bold text-yellow-500">€{{ number_format($total, 2) }}</span>
                </div>
            </div>

            <!-- Información de envío -->
            <div class="bg-gray-900/60 rounded-xl p-6 border border-gray-800">
                <h2 class="text-lg font-semibold mb-4 text-yellow-500">Información de Envío</h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-xs tracking-widest text-gray-400 mb-2 uppercase">Dirección</label>
                        <input type="text" name="shipping_address" required minlength="10" maxlength="255" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white text-sm focus:border-yellow-500 focus:outline-none transition-all" placeholder="Calle, número, piso...">
                    </div>
                    <div>
                        <label class="block text-xs tracking-widest text-gray-400 mb-2 uppercase">Ciudad</label>
                        <input type="text" name="shipping_city" required minlength="2" maxlength="100" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white text-sm focus:border-yellow-500 focus:outline-none transition-all" placeholder="Ciudad">
                    </div>
                    <div>
                        <label class="block text-xs tracking-widest text-gray-400 mb-2 uppercase">Código Postal</label>
                        <input type="text" name="shipping_postal_code" required pattern="[0-9]{5}" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white text-sm focus:border-yellow-500 focus:outline-none transition-all" placeholder="28001">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs tracking-widest text-gray-400 mb-2 uppercase">Teléfono</label>
                        <input type="tel" name="shipping_phone" required pattern="(\+34|0034)?[6-9][0-9]{8}" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white text-sm focus:border-yellow-500 focus:outline-none transition-all" placeholder="+34 600 000 000">
                    </div>
                </div>
            </div>

            <!-- Método de pago -->
            <div class="bg-gray-900/60 rounded-xl p-6 border border-gray-800">
                <h2 class="text-lg font-semibold mb-4 text-yellow-500">Método de Pago</h2>
                
                <div class="space-y-3">
                    <label class="flex items-center gap-3 p-4 border border-gray-700 rounded-lg cursor-pointer hover:border-yellow-500 transition-all">
                        <input type="radio" name="payment_method" value="card" required class="w-4 h-4 accent-yellow-500">
                        <div>
                            <p class="font-medium text-sm">Tarjeta de crédito/débito</p>
                            <p class="text-gray-400 text-xs">Visa, Mastercard, American Express</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-4 border border-gray-700 rounded-lg cursor-pointer hover:border-yellow-500 transition-all">
                        <input type="radio" name="payment_method" value="cash" required class="w-4 h-4 accent-yellow-500">
                        <div>
                            <p class="font-medium text-sm">Contrareembolso</p>
                            <p class="text-gray-400 text-xs">Paga al recibir el pedido</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Botón de confirmar -->
            <div class="flex gap-4">
                <a href="{{ route('cart.index') }}" class="flex-1 bg-gray-800 text-white py-4 rounded-lg text-center text-sm tracking-widest uppercase hover:bg-gray-700 transition-all">Volver al carrito</a>
                <button type="submit" class="flex-1 bg-yellow-500 text-black py-4 rounded-lg text-sm tracking-widest uppercase font-medium hover:bg-yellow-400 transition-all">Confirmar Pedido</button>
            </div>
        </form>

    </div>
</main>

@endsection
