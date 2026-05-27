@extends('taller.layout')

@section('content')

<div class="mb-6 flex items-end justify-between">
    <div>
        <h1 class="text-base font-bold tracking-widest uppercase text-white mb-1">Mis anuncios</h1>
        <p class="text-xs text-white/40">Gestiona los artículos que has publicado.</p>
    </div>
    <a href="{{ route('segundamano.crear') }}"
       class="px-4 py-2 border border-[#f0c36d]/50 text-[#f0c36d] text-[10px] tracking-widest uppercase
              hover:bg-[#f0c36d]/10 transition-all rounded">
        + Nuevo anuncio
    </a>
</div>

@if($anuncios->isEmpty())
    <div class="px-4 py-8 rounded-[10px] border border-white/10 bg-[#111] text-center">
        <p class="text-xs text-white/30 uppercase tracking-widest mb-4">No tienes anuncios publicados</p>
        <a href="{{ route('segundamano.crear') }}"
           class="inline-block px-5 py-2 border border-[#f0c36d]/40 text-[#f0c36d] text-[10px] tracking-widest uppercase
                  hover:bg-[#f0c36d]/10 transition-all">
            Publicar mi primer anuncio
        </a>
    </div>
@else
    <div class="flex flex-col gap-2">
        @foreach($anuncios as $anuncio)
            <div class="flex items-center gap-4 bg-[#111] border border-white/10 rounded-[10px] px-4 py-3">

                {{-- Miniatura --}}
                <div class="w-14 h-14 rounded bg-white/5 overflow-hidden shrink-0">
                    @if($anuncio->imagen)
                        <img src="{{ asset('storage/' . $anuncio->imagen) }}"
                             alt="{{ $anuncio->titulo }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-white/20 text-[9px] uppercase">
                            —
                        </div>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-200 truncate">
                        {{ $anuncio->titulo }}
                    </p>
                    <p class="text-[10px] text-white/40 mt-0.5">
                        {{ $anuncio->categoria ? ucfirst($anuncio->categoria) . ' · ' : '' }}
                        {{ $anuncio->etiquetaEstado() }}
                    </p>
                </div>

                {{-- Precio --}}
                <p class="text-[#f0c36d] text-sm font-light shrink-0">
                    €{{ number_format($anuncio->precio, 2) }}
                </p>

                {{-- Estado badges --}}
                <div class="flex gap-2 shrink-0">
                    @if($anuncio->vendido)
                        <span class="px-2 py-0.5 rounded text-[10px] tracking-widest uppercase border border-red-500/30 text-red-400">
                            Vendido
                        </span>
                    @elseif($anuncio->activo)
                        <span class="px-2 py-0.5 rounded text-[10px] tracking-widest uppercase border border-green-500/30 text-green-400">
                            Activo
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded text-[10px] tracking-widest uppercase border border-white/10 text-white/30">
                            Oculto
                        </span>
                    @endif
                </div>

                {{-- Acciones --}}
                <div class="flex gap-2 shrink-0">
                    <a href="{{ route('segundamano.show', $anuncio->id) }}"
                       class="px-3 py-1.5 text-[10px] uppercase tracking-widest border border-white/10 text-white/40
                              rounded hover:border-white/30 hover:text-white transition-all">
                        Ver
                    </a>
                    <a href="{{ route('segundamano.editar', $anuncio->id) }}"
                       class="px-3 py-1.5 text-[10px] uppercase tracking-widest border border-[#f0c36d]/30 text-[#f0c36d]
                              rounded hover:bg-[#f0c36d]/10 transition-all">
                        Editar
                    </a>
                    <form action="{{ route('segundamano.destroy', $anuncio->id) }}" method="POST"
                          onsubmit="return confirm('¿Eliminar este anuncio?')">
                        @csrf
                        <button type="submit"
                                class="px-3 py-1.5 text-[10px] uppercase tracking-widest border border-red-500/20 text-red-500/60
                                       rounded hover:bg-red-500/10 hover:text-red-400 transition-all">
                            Borrar
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif

@endsection
