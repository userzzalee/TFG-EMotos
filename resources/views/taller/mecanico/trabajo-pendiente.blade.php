@extends('taller.layout')

@section('content')

<h1 class="text-2xl font-bold tracking-widest uppercase text-white mb-1">Trabajo pendiente</h1>
<p class="text-sm text-white/40 mb-8">Citas que has aceptado y están en curso.</p>

@forelse($citas as $cita)
    <a href="{{ route('taller.detalle-cita', $cita) }}"
       class="flex items-center justify-between px-5 py-4 bg-[#111] border border-white/10
              rounded-xl mb-3 hover:border-[#f0c36d]/30 hover:bg-white/[.03] transition-all group">

        <div class="flex items-center gap-4">
            <div class="w-9 h-9 rounded-full bg-white/5 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-white/40 group-hover:text-[#f0c36d] transition-colors"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-white text-sm font-semibold">{{ $cita->marca }} {{ $cita->modelo }}
                    <span class="text-white/40 font-normal ml-1">{{ $cita->matricula }}</span>
                </p>
                <p class="text-xs text-white/40 mt-0.5">{{ $cita->usuario->name }} · Aceptada {{ $cita->updated_at->diffForHumans() }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span class="text-xs border rounded px-2 py-1 {{ $cita->colorEstado() }}">{{ $cita->etiquetaEstado() }}</span>
            <svg class="w-4 h-4 text-white/20 group-hover:text-[#f0c36d] transition-colors"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </div>
    </a>
@empty
    <div class="text-center py-20 text-white/20">
        <svg class="w-12 h-12 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M5 13l4 4L19 7"/>
        </svg>
        <p class="text-sm uppercase tracking-widest">Sin trabajo pendiente</p>
    </div>
@endforelse

@endsection
