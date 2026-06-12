<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $anuncio->titulo }} – Alyx</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="m-0 bg-black min-h-screen overflow-x-hidden">

@include('layouts.navigation')

<div class="fixed inset-0 bg-gradient-to-br from-gray-900 via-black to-gray-900 z-0 pointer-events-none"></div>

<main class="relative z-10 text-white pt-[75px] pb-20">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">

        {{-- Breadcrumb --}}
        <p class="text-[10px] tracking-widest text-gray-600 uppercase mb-6 truncate">
            <a href="{{ route('segundamano.index') }}" class="hover:text-yellow-500 transition-colors">Segunda Mano</a>
            <span class="mx-2 text-gray-700">›</span>
            {{ $anuncio->titulo }}
        </p>

        {{-- Grid: columna en móvil, 2 columnas en md+ --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-12">

            {{-- Galería --}}
            @php
                $galeria = $anuncio->imagenes->pluck('ruta');
                if ($galeria->isEmpty() && $anuncio->imagen) {
                    $galeria = collect([$anuncio->imagen]);
                }
            @endphp

            <div x-data="{ activa: '{{ $galeria->first() ? asset('storage/' . $galeria->first()) : '' }}' }">
                <div class="aspect-square bg-gray-900 overflow-hidden rounded">
                    @if($galeria->isNotEmpty())
                        <img :src="activa" alt="" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-700 text-xs tracking-widest uppercase">Sin imagen</div>
                    @endif
                </div>

                @if($galeria->count() > 1)
                    <div class="grid grid-cols-5 gap-2 mt-3">
                        @foreach($galeria as $ruta)
                            @php $url = asset('storage/' . $ruta); @endphp
                            <button type="button" @click="activa = '{{ $url }}'"
                                    class="aspect-square bg-gray-900 overflow-hidden rounded border transition-all"
                                    :class="activa === '{{ $url }}' ? 'border-yellow-500' : 'border-transparent hover:border-white/20'">
                                <img src="{{ $url }}" alt="" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Datos --}}
            <div class="flex flex-col">

                <div class="flex flex-wrap gap-2 mb-4">
                    @if($anuncio->categoria)
                        <span class="px-3 py-0.5 border border-gray-700 text-[10px] tracking-widest uppercase text-gray-400 rounded">
                            {{ $anuncio->categoria }}
                        </span>
                    @endif
                    <span class="px-3 py-0.5 border border-yellow-500/30 text-[10px] tracking-widest uppercase text-yellow-500/70 rounded">
                        {{ $anuncio->etiquetaEstado() }}
                    </span>
                    @if($anuncio->vendido)
                        <span class="px-3 py-0.5 border border-red-500/40 text-[10px] tracking-widest uppercase text-red-400 rounded">Vendido</span>
                    @endif
                </div>

                <h1 class="text-xl sm:text-2xl font-light tracking-[0.15em] uppercase mb-3">{{ $anuncio->titulo }}</h1>
                <p class="text-2xl sm:text-3xl text-yellow-500 font-light mb-4 sm:mb-6">€{{ number_format($anuncio->precio, 2) }}</p>

                @if($anuncio->descripcion)
                    <p class="text-gray-400 text-sm leading-relaxed mb-6 sm:mb-8">{{ $anuncio->descripcion }}</p>
                @endif

                {{-- Vendedor --}}
                <div class="border-t border-white/5 pt-4 mb-6">
                    <p class="text-[10px] tracking-widest text-gray-600 uppercase mb-1">Vendedor</p>
                    <p class="text-sm text-gray-300">{{ $anuncio->vendedor->name }}</p>
                    @php
                        $notaVendedor = $anuncio->vendedor->notaMedia();
                        $totalVendedor = $anuncio->vendedor->totalValoraciones();
                    @endphp
                    @if($notaVendedor !== null)
                        <div class="flex items-center gap-2 mt-1 flex-wrap">
                            @include('segundamano.partials.estrellas', ['nota' => $notaVendedor, 'size' => 'text-xs'])
                            <span class="text-[11px] text-gray-400">{{ number_format($notaVendedor, 1) }}</span>
                            <span class="text-[10px] text-gray-600">({{ $totalVendedor }} {{ $totalVendedor === 1 ? 'valoración' : 'valoraciones' }})</span>
                        </div>
                    @else
                        <p class="text-[10px] text-gray-600 mt-1">Sin valoraciones todavía</p>
                    @endif
                    <p class="text-[10px] text-gray-600 mt-0.5">Publicado {{ $anuncio->created_at->diffForHumans() }}</p>
                </div>

                {{-- Acciones --}}
                <div class="flex flex-col gap-3 mt-auto">
                    @auth
                        @if(Auth::id() === $anuncio->user_id)
                            <a href="{{ route('segundamano.editar', $anuncio->id) }}"
                               class="w-full py-3 border border-yellow-500 text-yellow-500 text-xs tracking-widest uppercase text-center hover:bg-yellow-500 hover:text-black transition-all">
                               Editar anuncio
                            </a>
                            <form action="{{ route('segundamano.destroy', $anuncio->id) }}" method="POST"
                                  onsubmit="return confirm('¿Eliminar este anuncio?')">
                                @csrf
                                <button type="submit"
                                        class="w-full py-3 border border-red-500/30 text-red-400 text-xs tracking-widest uppercase hover:bg-red-500/10 transition-all">
                                    Eliminar anuncio
                                </button>
                            </form>
                        @elseif(!$anuncio->vendido)
                            @if($conversacionExistente)
                                <a href="{{ route('chat.show', $conversacionExistente->id) }}"
                                   class="w-full py-3 border border-yellow-500 text-yellow-500 text-xs tracking-widest uppercase text-center hover:bg-yellow-500 hover:text-black transition-all">
                                   Continuar conversación
                                </a>
                            @else
                                <form action="{{ route('segundamano.contactar', $anuncio->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="w-full py-3 bg-yellow-500 text-black text-xs tracking-widest uppercase font-medium hover:bg-yellow-400 transition-all">
                                        Contactar con el vendedor
                                    </button>
                                </form>
                            @endif
                        @else
                            <p class="text-center text-xs tracking-widest text-gray-600 uppercase py-3 border border-white/5 rounded">
                                Este artículo ya está vendido
                            </p>
                        @endif
                    @else
                        <a href="{{ route('login') }}"
                           class="w-full py-3 border border-yellow-500 text-yellow-500 text-xs tracking-widest uppercase text-center hover:bg-yellow-500 hover:text-black transition-all">
                           Inicia sesión para contactar
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        {{-- Valoraciones --}}
        <div class="mt-12 sm:mt-16 border-t border-white/5 pt-8 sm:pt-10">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2 mb-6">
                <p class="text-[10px] tracking-[0.3em] text-gray-600 uppercase">Valoraciones del vendedor</p>
                @if($anuncio->vendedor->notaMedia() !== null)
                    <div class="flex items-center gap-2">
                        @include('segundamano.partials.estrellas', ['nota' => $anuncio->vendedor->notaMedia(), 'size' => 'text-sm'])
                        <span class="text-sm text-gray-300">{{ number_format($anuncio->vendedor->notaMedia(), 1) }} / 5</span>
                    </div>
                @endif
            </div>

            @auth
                @if($puedeValorar)
                    <div x-data="{ puntuacion: 0, hover: 0 }"
                         class="border border-white/10 rounded p-4 sm:p-5 mb-8 bg-white/[0.02]">
                        <p class="text-xs text-gray-300 mb-3 tracking-wide">¿Cómo fue tu experiencia con {{ $anuncio->vendedor->name }}?</p>
                        <form action="{{ route('segundamano.valorar', $anuncio->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="puntuacion" :value="puntuacion">
                            <div class="flex gap-1 mb-3 text-2xl leading-none" @mouseleave="hover = 0">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button"
                                            @click="puntuacion = {{ $i }}"
                                            @mouseenter="hover = {{ $i }}"
                                            class="transition-colors focus:outline-none"
                                            :class="(hover || puntuacion) >= {{ $i }} ? 'text-yellow-500' : 'text-gray-700'">★</button>
                                @endfor
                            </div>
                            @error('puntuacion') <p class="text-[11px] text-red-400 mb-2">{{ $message }}</p> @enderror
                            <textarea name="comentario" rows="3" maxlength="1000"
                                      placeholder="Cuenta cómo fue la compra (opcional)…"
                                      class="w-full bg-gray-900 border border-gray-700 text-white text-xs px-3 py-2 rounded focus:border-yellow-500 focus:outline-none transition-all placeholder-gray-600 mb-3">{{ old('comentario') }}</textarea>
                            <button type="submit"
                                    :disabled="puntuacion === 0"
                                    class="px-5 py-2 bg-yellow-500 text-black text-xs tracking-widest uppercase font-medium hover:bg-yellow-400 transition-all disabled:opacity-40 disabled:cursor-not-allowed">
                                Enviar valoración
                            </button>
                        </form>
                    </div>
                @endif
            @endauth

            @forelse($anuncio->vendedor->valoracionesRecibidas()->with('autor')->latest()->take(10)->get() as $val)
                <div class="border-b border-white/5 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 mb-1">
                        <span class="text-xs text-gray-300">{{ $val->autor->name ?? 'Usuario' }}</span>
                        <div class="flex items-center gap-2">
                            @include('segundamano.partials.estrellas', ['nota' => $val->puntuacion, 'size' => 'text-xs'])
                            <span class="text-[10px] text-gray-600">{{ $val->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    @if($val->comentario)
                        <p class="text-xs text-gray-400 leading-relaxed">{{ $val->comentario }}</p>
                    @endif
                </div>
            @empty
                <p class="text-xs text-gray-600">Este vendedor todavía no tiene valoraciones.</p>
            @endforelse
        </div>

        {{-- Otros anuncios del vendedor --}}
        @php
            $otrosAnuncios = \App\Models\Anuncio::disponibles()
                ->where('user_id', $anuncio->user_id)
                ->where('id', '!=', $anuncio->id)
                ->latest()->take(3)->get();
        @endphp

        @if($otrosAnuncios->count() > 0)
            <div class="mt-12 sm:mt-16 border-t border-white/5 pt-8 sm:pt-10">
                <p class="text-[10px] tracking-[0.3em] text-gray-600 uppercase mb-6">
                    Más anuncios de {{ $anuncio->vendedor->name }}
                </p>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-5">
                    @foreach($otrosAnuncios as $otro)
                        <a href="{{ route('segundamano.show', $otro->id) }}" class="group block no-underline">
                            <div class="aspect-[4/3] bg-gray-900 overflow-hidden mb-2">
                                @if($otro->imagen)
                                    <img src="{{ asset('storage/' . $otro->imagen) }}" alt=""
                                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-700 text-[10px] uppercase tracking-widest">Sin imagen</div>
                                @endif
                            </div>
                            <p class="text-xs text-white truncate">{{ $otro->titulo }}</p>
                            <p class="text-yellow-500 text-sm font-light">€{{ number_format($otro->precio, 2) }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</main>

</body>
</html>
