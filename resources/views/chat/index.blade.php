@extends('layouts.general')

@section('content')

<div class="mb-6">
    <h1 class="text-base font-bold tracking-widest uppercase text-white mb-1">Mis conversaciones</h1>
    <p class="text-xs text-white/40">Mensajes con compradores y vendedores.</p>
</div>

@if($conversaciones->isEmpty())
    <div class="px-4 py-6 rounded-[10px] border border-white/10 bg-[#111] text-center text-xs text-white/40 uppercase tracking-widest">
        No tienes ninguna conversación todavía.
    </div>
@else
    <div class="flex flex-col gap-2">
        @foreach($conversaciones as $conv)
            @php
                $otro     = $conv->otroParticipante($userId);
                $noLeidos = $conv->mensajesNoLeidos($userId);
                $ultimo   = $conv->ultimoMensaje->first();
            @endphp

            <a href="{{ route('chat.show', $conv->id) }}"
                class="flex items-center justify-between gap-4 bg-[#111] border border-white/10 rounded-[10px] px-4 py-3
                    no-underline transition-all duration-200 hover:border-[rgba(240,195,109,0.35)] hover:-translate-y-0.5 group">

                {{-- Avatar inicial + nombre --}}
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-[#f0c36d]/10 border border-[#f0c36d]/20 flex items-center justify-center
                                text-[#f0c36d] text-xs font-bold uppercase shrink-0">
                        {{ mb_substr($otro->name, 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-widest text-gray-200 truncate">
                            {{ $otro->name }}
                            @if($conv->producto)
                                <span class="text-white/30 font-normal normal-case tracking-normal">
                                    — {{ $conv->producto->titulo }}
                                </span>
                            @endif
                        </p>
                        <p class="text-xs text-white/40 truncate mt-0.5">
                            {{ $ultimo ? Str::limit($ultimo->contenido, 60) : 'Sin mensajes aún' }}
                        </p>
                    </div>
                </div>

                {{-- Badge no leídos + hora --}}
                <div class="flex flex-col items-end shrink-0 gap-1">
                    @if($noLeidos > 0)
                        <span class="px-2 py-0.5 rounded-full bg-[#f0c36d]/20 text-[#f0c36d] text-xs font-bold">
                            {{ $noLeidos }}
                        </span>
                    @endif
                    <span class="text-[10px] text-white/25">
                        {{ $conv->ultimo_mensaje_at?->diffForHumans() }}
                    </span>
                </div>
            </a>
        @endforeach
    </div>
@endif

@endsection
