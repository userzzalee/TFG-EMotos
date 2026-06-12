@extends('layouts.general')

@section('content')

<div class="fixed inset-0 bg-gradient-to-br from-gray-900 via-black to-gray-900 z-0 pointer-events-none"></div>

<div class="relative z-10 text-white max-w-4xl mx-auto px-8 -mt-8">

    @if(session('success'))
        <div class="bg-green-900/50 border border-green-500 text-green-400 px-6 py-4 rounded-lg mb-8 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('order.index') }}"
           class="text-[10px] uppercase tracking-widest text-white/40 hover:text-[#f0c36d] transition-colors">
            ← Volver a mis pedidos
        </a>
        <a href="{{ route('order.factura', $order->id) }}"
           class="inline-flex items-center gap-2 px-4 py-2 border border-yellow-500 text-yellow-500 text-[10px] uppercase tracking-widest hover:bg-yellow-500 hover:text-black transition-all rounded">
            ↓ Descargar factura
        </a>
    </div>

    <p class="text-[10px] tracking-[0.3em] text-yellow-500 uppercase mb-2">Pedido #{{ $order->id }}</p>
    <h1 class="text-3xl font-light tracking-[0.3em] mb-8 uppercase">Detalle del Pedido</h1>

    <div class="grid grid-cols-2 gap-6">
        <!-- Información del pedido -->
        <div class="bg-gray-900/60 rounded-xl p-6 border border-gray-800">
            <h2 class="text-lg font-semibold mb-4 text-yellow-500">Información</h2>
            <div class="space-y-3 text-sm">
                <div>
                    <p class="text-gray-400 text-xs">Estado</p>
                    <p class="font-medium">{{ match($order->status) { 'pending' => 'Pendiente', 'completed' => 'Completado', 'cancelled' => 'Cancelado', default => ucfirst($order->status) } }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs">Fecha</p>
                    <p class="font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs">Método de pago</p>
                    <p class="font-medium">{{ $order->payment_method === 'card' ? 'Tarjeta' : 'Contrareembolso' }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs">Total</p>
                    <p class="font-bold text-yellow-500 text-lg">€{{ number_format($order->total, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Dirección de envío -->
        <div class="bg-gray-900/60 rounded-xl p-6 border border-gray-800">
            <h2 class="text-lg font-semibold mb-4 text-yellow-500">Dirección de Envío</h2>
            <div class="space-y-2 text-sm">
                <p class="font-medium">{{ $order->shipping_address }}</p>
                <p class="text-gray-400">{{ $order->shipping_city }}</p>
                <p class="text-gray-400">{{ $order->shipping_postal_code }}</p>
                <p class="text-gray-400">{{ $order->shipping_phone }}</p>
            </div>
        </div>
    </div>

    <!-- Productos del pedido -->
    <div class="bg-gray-900/60 rounded-xl p-6 border border-gray-800 mt-6">
        <h2 class="text-lg font-semibold mb-4 text-yellow-500">Productos</h2>
        <div class="space-y-3">
            @foreach($order->items as $item)
                <div class="flex justify-between items-center text-sm border-b border-gray-700 pb-3 last:border-0 last:pb-0">
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
    </div>

</div>

@endsection
