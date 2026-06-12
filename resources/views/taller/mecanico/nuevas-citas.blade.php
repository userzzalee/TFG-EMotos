@extends('taller.layout')

@section('content')

<h1 class="text-2xl font-bold tracking-widest uppercase text-white mb-1">Nuevas citas</h1>
<p class="text-sm text-white/40 mb-8">Solicitudes recibidas pendientes de aceptar.</p>

@forelse($citas as $cita)
    <div x-data="{ open: false }"
         class="border border-white/10 rounded-xl mb-4 overflow-hidden bg-[#111]">

        {{-- Cabecera (siempre visible) --}}
        <button type="button" @click="open = !open"
                class="w-full flex items-center justify-between px-6 py-4 hover:bg-white/[.03] transition-colors text-left">
            <div class="flex items-center gap-4">
                <div class="w-8 h-8 rounded-full bg-[#f0c36d]/10 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-[#f0c36d]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white font-semibold text-sm">{{ $cita->marca }} {{ $cita->modelo }}</p>
                    <p class="text-white/40 text-xs">{{ $cita->matricula }} · {{ $cita->usuario->name }} · {{ $cita->created_at->diffForHumans() }}</p>
                    @if($cita->fecha_cita)
                        <p class="text-[#f0c36d] text-xs mt-0.5">{{ $cita->fechaCitaLegible() }}</p>
                    @endif
                </div>
            </div>
            <svg class="w-4 h-4 text-white/40 transition-transform" :class="open ? 'rotate-180' : ''"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        {{-- Detalle expandible --}}
        <div x-show="open" x-transition class="px-6 pb-6 border-t border-white/[.07]">
            <div class="pt-5 grid grid-cols-2 gap-5">
                <div>
                    <p class="detail-label">Cliente</p>
                    <p class="detail-value">{{ $cita->usuario->name }}</p>
                    <p class="text-xs text-white/30">{{ $cita->usuario->email }}</p>
                </div>
                <div>
                    <p class="detail-label">Vehículo</p>
                    <p class="detail-value">{{ $cita->marca }} {{ $cita->modelo }} — {{ $cita->matricula }}</p>
                </div>
                @if($cita->fecha_cita)
                    <div>
                        <p class="detail-label">Fecha solicitada</p>
                        <p class="detail-value text-[#f0c36d]">{{ $cita->fechaCitaLegible() }}</p>
                    </div>
                @endif
                <div class="col-span-2">
                    <p class="detail-label">Problema</p>
                    <p class="detail-value text-white/80">{{ $cita->problema }}</p>
                </div>
                @if($cita->comentarios)
                    <div class="col-span-2">
                        <p class="detail-label">Comentarios del cliente</p>
                        <p class="detail-value text-white/60">{{ $cita->comentarios }}</p>
                    </div>
                @endif
                @if($cita->fotos && count($cita->fotos))
                    <div class="col-span-2">
                        <p class="detail-label">Fotos</p>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach($cita->fotos as $foto)
                                <a href="{{ Storage::url($foto) }}" target="_blank">
                                    <img src="{{ Storage::url($foto) }}" alt="foto"
                                         class="w-20 h-20 object-cover rounded-lg border border-white/10 hover:border-[#f0c36d]/50 transition-colors">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Acción --}}
            <div class="mt-6 flex gap-3">
                <form action="{{ route('taller.aceptar', $cita) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="px-6 py-2.5 bg-[#f0c36d] hover:bg-[#e0b35d] text-black text-xs font-bold uppercase tracking-widest rounded transition-colors">
                        Aceptar cita
                    </button>
                </form>
            </div>
        </div>
    </div>
@empty
    <div class="min-h-[400px] flex items-center justify-center text-white/20">
        <p class="text-sm uppercase tracking-widest">Sin nuevas citas</p>
    </div>
@endforelse

<style>
    .detail-label { font-size:10px; text-transform:uppercase; letter-spacing:.08em; color:#6b7280; margin-bottom:3px; }
    .detail-value { font-size:14px; color:#e5e7eb; }
</style>
@endsection
