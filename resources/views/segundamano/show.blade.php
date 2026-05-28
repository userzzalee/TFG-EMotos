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
    <div class="max-w-5xl mx-auto px-6">

        {{-- Breadcrumb --}}
        <p class="text-[10px] tracking-widest text-gray-600 uppercase mb-8">
            <a href="{{ route('segundamano.index') }}" class="hover:text-yellow-500 transition-colors">
                Segunda Mano
            </a>
            <span class="mx-2 text-gray-700">›</span>
            {{ $anuncio->titulo }}
        </p>

        <div class="grid grid-cols-2 gap-12">

            {{-- Imagen --}}
            <div class="aspect-square bg-gray-900 overflow-hidden rounded">
                @if($anuncio->imagen)
                    <img src="{{ asset('storage/' . $anuncio->imagen) }}"
                         alt=""
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-700 text-xs tracking-widest uppercase">
                        Sin imagen
                    </div>
                @endif
            </div>

            {{-- Datos --}}
            <div class="flex flex-col">

                {{-- Badges --}}
                <div class="flex gap-2 mb-4">
                    @if($anuncio->categoria)
                        <span class="px-3 py-0.5 border border-gray-700 text-[10px] tracking-widest uppercase text-gray-400 rounded">
                            {{ $anuncio->categoria }}
                        </span>
                    @endif
                    <span class="px-3 py-0.5 border border-yellow-500/30 text-[10px] tracking-widest uppercase text-yellow-500/70 rounded">
                        {{ $anuncio->etiquetaEstado() }}
                    </span>
                    @if($anuncio->vendido)
                        <span class="px-3 py-0.5 border border-red-500/40 text-[10px] tracking-widest uppercase text-red-400 rounded">
                            Vendido
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl font-light tracking-[0.15em] uppercase mb-3">
                    {{ $anuncio->titulo }}
                </h1>

                <p class="text-3xl text-yellow-500 font-light mb-6">
                    €{{ number_format($anuncio->precio, 2) }}
                </p>

                @if($anuncio->descripcion)
                    <p class="text-gray-400 text-sm leading-relaxed mb-8">
                        {{ $anuncio->descripcion }}
                    </p>
                @endif

                {{-- Vendedor --}}
                <div class="border-t border-white/5 pt-4 mb-6">
                    <p class="text-[10px] tracking-widest text-gray-600 uppercase mb-1">Vendedor</p>
                    <p class="text-sm text-gray-300">{{ $anuncio->vendedor->name }}</p>
                    <p class="text-[10px] text-gray-600 mt-0.5">
                        Publicado {{ $anuncio->created_at->diffForHumans() }}
                    </p>
                </div>

                {{-- Acciones --}}
                <div class="flex flex-col gap-3 mt-auto">

                    @auth
                        @if(Auth::id() === $anuncio->user_id)
                            {{-- Dueño del anuncio --}}
                            <a href="{{ route('segundamano.editar', $anuncio->id) }}"
                               class="w-full py-3 border border-yellow-500 text-yellow-500 text-xs tracking-widest uppercase
                                      text-center hover:bg-yellow-500 hover:text-black transition-all">
                                Editar anuncio
                            </a>
                            <form action="{{ route('segundamano.destroy', $anuncio->id) }}" method="POST"
                                  onsubmit="return confirm('¿Eliminar este anuncio?')">
                                @csrf
                                <button type="submit"
                                        class="w-full py-3 border border-red-500/30 text-red-400 text-xs tracking-widest uppercase
                                               hover:bg-red-500/10 transition-all">
                                    Eliminar anuncio
                                </button>
                            </form>
                        @elseif(!$anuncio->vendido)
                            {{-- Comprador: contactar con el vendedor --}}
                            @if($conversacionExistente)
                                <a href="{{ route('chat.show', $conversacionExistente->id) }}"
                                   class="w-full py-3 border border-yellow-500 text-yellow-500 text-xs tracking-widest uppercase
                                          text-center hover:bg-yellow-500 hover:text-black transition-all">
                                    💬 Continuar conversación
                                </a>
                            @else
                                <form action="{{ route('segundamano.contactar', $anuncio->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="w-full py-3 bg-yellow-500 text-black text-xs tracking-widest uppercase
                                                   font-medium hover:bg-yellow-400 transition-all">
                                        💬 Contactar con el vendedor
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
                           class="w-full py-3 border border-yellow-500 text-yellow-500 text-xs tracking-widest uppercase
                                  text-center hover:bg-yellow-500 hover:text-black transition-all">
                            Inicia sesión para contactar
                        </a>
                    @endauth
                </div>

            </div>
        </div>

        {{-- Otros anuncios del mismo vendedor --}}
        @php
            $otrosAnuncios = \App\Models\Anuncio::disponibles()
                ->where('user_id', $anuncio->user_id)
                ->where('id', '!=', $anuncio->id)
                ->latest()->take(3)->get();
        @endphp

        @if($otrosAnuncios->count() > 0)
            <div class="mt-16 border-t border-white/5 pt-10">
                <p class="text-[10px] tracking-[0.3em] text-gray-600 uppercase mb-6">
                    Más anuncios de {{ $anuncio->vendedor->name }}
                </p>
                <div class="grid grid-cols-3 gap-5">
                    @foreach($otrosAnuncios as $otro)
                        <a href="{{ route('segundamano.show', $otro->id) }}" class="group block no-underline">
                            <div class="aspect-[4/3] bg-gray-900 overflow-hidden mb-2">
                                @if($otro->imagen)
                                    <img src="{{ asset('storage/' . $otro->imagen) }}"
                                         alt=""
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
