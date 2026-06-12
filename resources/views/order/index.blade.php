@extends('layouts.general')

@section('content')

<div class="fixed inset-0 bg-gradient-to-br from-gray-900 via-black to-gray-900 z-0 pointer-events-none"></div>

<div class="relative z-10 text-white max-w-2xl mx-auto -mt-4">

    <p class="text-[10px] tracking-[0.3em] text-yellow-500 uppercase mb-2 text-center">Mi Cuenta</p>
    <h1 class="text-xl sm:text-2xl font-light tracking-[0.2em] mb-6 text-center uppercase">Mis Pedidos</h1>

    @if(session('success'))
        <div class="bg-green-900/50 border border-green-500 text-green-400 px-4 sm:px-6 py-4 rounded-lg mb-8 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($orders->count() > 0)
        <div class="space-y-3">
            @foreach($orders as $order)
                <a href="{{ route('order.show', $order->id) }}"
                   class="block bg-gray-900/60 rounded-xl p-4 sm:p-6 border border-gray-800 hover:border-yellow-500/50 transition-all">
                    <div class="flex justify-between items-start gap-3">
                        <div>
                            <p class="text-sm font-medium">Pedido #{{ $order->id }}</p>
                            <p class="text-gray-400 text-xs mt-1">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-yellow-500 font-bold">€{{ number_format($order->total, 2) }}</p>
                            <p class="text-xs mt-1 {{ $order->status === 'completed' ? 'text-green-400' : 'text-gray-400' }}">
                                {{ ucfirst($order->status) }}
                            </p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="text-center py-16">
            <p class="text-gray-400 text-lg">No tienes pedidos aún</p>
            <a href="{{ route('merchandising') }}"
               class="inline-block mt-4 px-6 py-2 bg-yellow-500 text-black rounded-xl font-medium hover:bg-yellow-400 transition-all text-sm">
               VER PRODUCTOS
            </a>
        </div>
    @endif
</div>

@endsection
