<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel Admin – Dashboard · Alyx</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="m-0 bg-black min-h-screen overflow-x-hidden">

@include('layouts.navigation')

<div class="fixed inset-0 bg-gradient-to-br from-gray-900 via-black to-gray-900 z-0 pointer-events-none"></div>

<main class="relative z-10 text-white pt-[75px] pb-20 px-6 lg:px-12">

    {{-- Cabecera --}}
    <div class="max-w-7xl mx-auto mb-8 flex items-end justify-between">
        <div>
            <p class="text-[10px] tracking-[0.3em] text-yellow-500 mb-1 uppercase">Administración</p>
            <h1 class="text-2xl font-light tracking-[0.2em] uppercase">Dashboard</h1>
        </div>
        <a href="{{ route('admin.usuarios') }}"
           class="px-5 py-2 border border-yellow-500 text-yellow-500 text-xs tracking-widest uppercase hover:bg-yellow-500 hover:text-black transition-all">
            Gestionar usuarios
        </a>
    </div>

    {{-- Tarjetas de resumen --}}
    <div class="max-w-7xl mx-auto grid grid-cols-2 lg:grid-cols-4 gap-5 mb-10">

        <div class="border border-white/10 rounded-lg p-5 bg-white/[0.02]">
            <p class="text-[10px] tracking-widest text-gray-500 uppercase mb-2">Ingresos taller</p>
            <p class="text-2xl font-light text-yellow-500">€{{ number_format($ingresosTaller, 2) }}</p>
            <p class="text-[10px] text-gray-600 mt-1">Citas pagadas</p>
        </div>

        <div class="border border-white/10 rounded-lg p-5 bg-white/[0.02]">
            <p class="text-[10px] tracking-widest text-gray-500 uppercase mb-2">Ingresos tienda</p>
            <p class="text-2xl font-light text-yellow-500">€{{ number_format($pedidos['ingresos'], 2) }}</p>
            <p class="text-[10px] text-gray-600 mt-1">{{ $pedidos['total'] }} pedidos</p>
        </div>

        <div class="border border-white/10 rounded-lg p-5 bg-white/[0.02]">
            <p class="text-[10px] tracking-widest text-gray-500 uppercase mb-2">Anuncios activos</p>
            <p class="text-2xl font-light text-white">{{ $anuncios['activos'] }}</p>
            <p class="text-[10px] text-gray-600 mt-1">{{ $anuncios['vendidos'] }} vendidos · {{ $anuncios['total'] }} en total</p>
        </div>

        <div class="border border-white/10 rounded-lg p-5 bg-white/[0.02]">
            <p class="text-[10px] tracking-widest text-gray-500 uppercase mb-2">Usuarios</p>
            <p class="text-2xl font-light text-white">{{ $usuarios['total'] }}</p>
            <p class="text-[10px] text-gray-600 mt-1">{{ $usuarios['mecanico'] }} mecánicos · {{ $usuarios['admin'] }} admin</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-8">

        {{-- Citas del taller por estado --}}
        <div class="border border-white/10 rounded-lg p-6 bg-white/[0.02]">
            <p class="text-[10px] tracking-[0.3em] text-gray-500 uppercase mb-5">Citas del taller por estado</p>

            @php
                $etiquetas = [
                    'pendiente'  => 'Pendiente',
                    'aceptada'   => 'Aceptada',
                    'en_proceso' => 'En proceso',
                    'finalizada' => 'Finalizada',
                    'pagada'     => 'Pagada',
                ];
                $colores = [
                    'pendiente'  => 'bg-yellow-500',
                    'aceptada'   => 'bg-blue-500',
                    'en_proceso' => 'bg-orange-500',
                    'finalizada' => 'bg-green-500',
                    'pagada'     => 'bg-gray-400',
                ];
            @endphp

            @if($totalCitas > 0)
                <div class="space-y-4">
                    @foreach($etiquetas as $clave => $etiqueta)
                        @php
                            $valor = $citasPorEstado[$clave] ?? 0;
                            $pct   = $totalCitas > 0 ? round($valor / $totalCitas * 100) : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="text-gray-300">{{ $etiqueta }}</span>
                                <span class="text-gray-500">{{ $valor }} · {{ $pct }}%</span>
                            </div>
                            <div class="h-2 rounded-full bg-white/5 overflow-hidden">
                                <div class="h-full {{ $colores[$clave] }} rounded-full transition-all" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Otros estados no contemplados arriba --}}
                    @foreach($citasPorEstado as $clave => $valor)
                        @if(!isset($etiquetas[$clave]))
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="text-gray-300">{{ ucfirst($clave) }}</span>
                                    <span class="text-gray-500">{{ $valor }}</span>
                                </div>
                                <div class="h-2 rounded-full bg-white/5 overflow-hidden">
                                    <div class="h-full bg-white/30 rounded-full" style="width: {{ round($valor / $totalCitas * 100) }}%"></div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <p class="text-[10px] text-gray-600 mt-6 uppercase tracking-widest">Total: {{ $totalCitas }} citas</p>
            @else
                <p class="text-xs text-gray-600">No hay citas registradas todavía.</p>
            @endif
        </div>

        {{-- Resumen rápido --}}
        <div class="border border-white/10 rounded-lg p-6 bg-white/[0.02]">
            <p class="text-[10px] tracking-[0.3em] text-gray-500 uppercase mb-5">Resumen</p>
            <div class="divide-y divide-white/5 text-sm">
                <div class="flex items-center justify-between py-3">
                    <span class="text-gray-400">Ingresos totales</span>
                    <span class="text-yellow-500">€{{ number_format($ingresosTaller + $pedidos['ingresos'], 2) }}</span>
                </div>
                <div class="flex items-center justify-between py-3">
                    <span class="text-gray-400">Citas totales</span>
                    <span class="text-gray-200">{{ $totalCitas }}</span>
                </div>
                <div class="flex items-center justify-between py-3">
                    <span class="text-gray-400">Pedidos de tienda</span>
                    <span class="text-gray-200">{{ $pedidos['total'] }}</span>
                </div>
                <div class="flex items-center justify-between py-3">
                    <span class="text-gray-400">Anuncios activos</span>
                    <span class="text-gray-200">{{ $anuncios['activos'] }}</span>
                </div>
                <div class="flex items-center justify-between py-3">
                    <span class="text-gray-400">Anuncios vendidos</span>
                    <span class="text-gray-200">{{ $anuncios['vendidos'] }}</span>
                </div>
                <div class="flex items-center justify-between py-3">
                    <span class="text-gray-400">Usuarios registrados</span>
                    <span class="text-gray-200">{{ $usuarios['total'] }}</span>
                </div>
            </div>
        </div>
    </div>

</main>

</body>
</html>
