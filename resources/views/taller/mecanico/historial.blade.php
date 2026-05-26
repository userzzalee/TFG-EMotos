@extends('taller.layout')

@section('content')

<h1 class="text-lg font-bold tracking-widest uppercase text-white mb-1">Historial de trabajo</h1>
<p class="text-xs text-white/40 mb-5">Todas las citas que has completado.</p>

@if($citas->count())
    {{-- Resumen rápido --}}
    <div class="grid grid-cols-2 gap-3 mb-5">
        <div class="bg-[#111] border border-white/10 rounded-lg px-4 py-3">
            <p class="text-[10px] uppercase tracking-widest text-white/30 mb-0.5">Total citas</p>
            <p class="text-lg font-bold text-white">{{ $citas->count() }}</p>
        </div>
        <div class="bg-[#111] border border-white/10 rounded-lg px-4 py-3">
            <p class="text-[10px] uppercase tracking-widest text-white/30 mb-0.5">Facturado</p>
            <p class="text-lg font-bold text-[#f0c36d]">{{ number_format($citas->where('estado', 'finalizada')->sum('coste'), 2) }} €</p>
        </div>
    </div>
@endif

<div class="overflow-x-auto">
    <table class="w-full text-xs">
        <thead>
            <tr class="border-b border-white/10 text-white/30 text-[10px] uppercase tracking-widest">
                <th class="text-left pb-2 pr-3 font-normal">Vehículo</th>
                <th class="text-left pb-2 pr-3 font-normal">Cliente</th>
                <th class="text-left pb-2 pr-3 font-normal">Fecha</th>
                <th class="text-right pb-2 pr-3 font-normal">Coste</th>
                <th class="text-right pb-2 font-normal">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($citas as $cita)
                <tr class="border-b border-white/[.06] hover:bg-white/[.02] transition-colors">
                    <td class="py-2.5 pr-3">
                        <p class="text-white text-xs">{{ $cita->marca }} {{ $cita->modelo }}</p>
                        <p class="text-white/30 text-[10px]">{{ $cita->matricula }}</p>
                    </td>
                    <td class="py-2.5 pr-3 text-white/60 text-xs">{{ $cita->usuario->name }}</td>
                    <td class="py-2.5 pr-3 text-white/40 text-xs">{{ $cita->updated_at->format('d/m/Y') }}</td>
                    <td class="py-2.5 pr-3 text-right font-semibold text-white text-xs">
                        {{ number_format($cita->coste, 2) }} €
                    </td>
                    <td class="py-2.5 text-right">
                        <span class="text-[10px] border rounded px-1.5 py-0.5 {{ $cita->colorEstado() }}">
                            {{ $cita->etiquetaEstado() }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-12 text-center text-white/20 text-xs uppercase tracking-widest">
                        Sin historial aún
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
