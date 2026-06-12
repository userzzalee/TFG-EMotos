@extends('taller.layout')

@section('content')

<h1 class="text-lg font-bold tracking-widest uppercase text-white mb-1">Trabajo pendiente</h1>
<p class="text-xs text-white/40 mb-5">Citas que has aceptado y están en curso.</p>

@forelse($citas as $cita)
    <a href="{{ route('taller.detalle-cita', $cita) }}"
       class="flex items-center justify-between px-4 py-3 bg-[#111] border border-white/10
              rounded-lg mb-2 hover:border-[#f0c36d]/30 hover:bg-white/[.03] transition-all group">

        <div class="flex items-center gap-3">
            <div class="w-7 h-7 rounded-full bg-white/5 flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5 text-white/40 group-hover:text-[#f0c36d] transition-colors"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-white text-xs font-semibold">{{ $cita->marca }} {{ $cita->modelo }}
                    <span class="text-white/40 font-normal ml-1">{{ $cita->matricula }}</span>
                </p>
                <p class="text-[10px] text-white/40 mt-0.5">{{ $cita->usuario->name }} · Aceptada {{ $cita->updated_at->diffForHumans() }}</p>
                @if($cita->fecha_cita)
                    <p class="text-[10px] text-[#f0c36d] mt-0.5">{{ $cita->fechaCitaLegible() }}</p>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-[10px] border rounded px-1.5 py-0.5 {{ $cita->colorEstado() }}">{{ $cita->etiquetaEstado() }}</span>
            <svg class="w-3.5 h-3.5 text-white/20 group-hover:text-[#f0c36d] transition-colors"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </div>
    </a>
@empty
    <div class="min-h-[300px] flex items-center justify-center text-white/20">
        <p class="text-xs uppercase tracking-widest">Sin trabajo pendiente</p>
    </div>
@endforelse

@endsection
