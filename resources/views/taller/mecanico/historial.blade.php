@extends('taller.layout')

@section('content')

<h1 class="text-2xl font-bold tracking-widest uppercase text-white mb-1">Historial de trabajo</h1>
<p class="text-sm text-white/40 mb-8">Todas las citas que has completado.</p>

@if($citas->count())
    {{-- Resumen rápido --}}
    <div class="grid grid-cols-2 gap-4 mb-8">
        <div class="bg-[#111] border border-white/10 rounded-xl px-5 py-4">
            <p class="text-xs uppercase tracking-widest text-white/30 mb-1">Total citas</p>
            <p class="text-2xl font-bold text-white">{{ $citas->count() }}</p>
        </div>
        <div class="bg-[#111] border border-white/10 rounded-xl px-5 py-4">
            <p class="text-xs uppercase tracking-widest text-white/30 mb-1">Facturado</p>
            <p class="text-2xl font-bold text-[#f0c36d]">{{ number_format($citas->where('estado', 'finalizada')->sum('coste'), 2) }} €</p>
        </div>
    </div>
@endif

<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-white/10 text-white/30 text-xs uppercase tracking-widest">
                <th class="text-left pb-3 pr-4 font-normal">Vehículo</th>
                <th class="text-left pb-3 pr-4 font-normal">Cliente</th>
                <th class="text-left pb-3 pr-4 font-normal">Fecha</th>
                <th class="text-right pb-3 pr-4 font-normal">Coste</th>
                <th class="text-right pb-3 font-normal">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($citas as $cita)
                <tr class="border-b border-white/[.06] hover:bg-white/[.02] transition-colors">
                    <td class="py-3.5 pr-4">
                        <p class="text-white">{{ $cita->marca }} {{ $cita->modelo }}</p>
                        <p class="text-white/30 text-xs">{{ $cita->matricula }}</p>
                    </td>
                    <td class="py-3.5 pr-4 text-white/60">{{ $cita->usuario->name }}</td>
                    <td class="py-3.5 pr-4 text-white/40">{{ $cita->updated_at->format('d/m/Y') }}</td>
                    <td class="py-3.5 pr-4 text-right font-semibold text-white">
                        {{ number_format($cita->coste, 2) }} €
                    </td>
                    <td class="py-3.5 text-right">
                        <span class="text-xs border rounded px-2 py-1 {{ $cita->colorEstado() }}">
                            {{ $cita->etiquetaEstado() }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-16 text-center text-white/20 text-sm uppercase tracking-widest">
                        Sin historial aún
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
