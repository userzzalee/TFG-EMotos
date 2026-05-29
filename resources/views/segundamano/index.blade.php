<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Segunda Mano – Alyx</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="m-0 bg-black min-h-screen overflow-x-hidden">

@include('layouts.navigation')

<div class="fixed inset-0 bg-gradient-to-br from-gray-900 via-black to-gray-900 z-0 pointer-events-none"></div>

<main class="relative z-10 text-white pt-[75px] pb-16">

    {{-- Cabecera --}}
    <div class="max-w-5xl mx-auto px-6 mb-8 flex items-end justify-between">
        <div>
            <p class="text-[10px] tracking-[0.3em] text-yellow-500 uppercase mb-1">Mercado</p>
            <h1 class="text-2xl font-light tracking-[0.2em] uppercase">Segunda Mano</h1>
        </div>
        @auth
            <a href="{{ route('segundamano.crear') }}" class="px-5 py-2 border border-yellow-500 text-yellow-500 text-xs tracking-widest hover:bg-yellow-500 hover:text-black transition-all">+ Publicar anuncio</a>
        @else
            <a href="{{ route('login') }}" class="px-5 py-2 border border-white/20 text-white/40 text-xs tracking-widest hover:border-yellow-500 hover:text-yellow-500 transition-all">Inicia sesión para vender</a>
        @endauth
    </div>

    {{-- Buscador + filtros --}}
    <div class="max-w-5xl mx-auto px-6 mb-8">
        <form method="GET" action="{{ route('segundamano.index') }}" class="flex flex-wrap gap-3 items-center">
            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar anuncios…" class="bg-gray-900 border border-gray-700 text-white text-xs px-4 py-2 rounded focus:border-yellow-500 focus:outline-none transition-all w-56 placeholder-gray-600">
            <select name="categoria" class="bg-gray-900 border border-gray-700 text-white text-xs px-4 py-2 rounded focus:border-yellow-500 focus:outline-none transition-all">
                <option value="">Todas las categorías</option>
                @foreach($categorias as $cat)
                    <option value="{{ $cat }}" {{ request('categoria') === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-5 py-2 border border-yellow-500 text-yellow-500 text-xs tracking-widest hover:bg-yellow-500 hover:text-black transition-all">Filtrar</button>
            @if(request()->hasAny(['buscar','categoria']))
                <a href="{{ route('segundamano.index') }}" class="text-xs text-gray-500 hover:text-white transition-colors tracking-widest">Limpiar</a>
            @endif
        </form>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="max-w-5xl mx-auto px-6 mb-6">
            <div class="border border-yellow-500/40 bg-yellow-500/10 text-yellow-400 px-4 py-3 rounded text-xs tracking-wide">
                {{ session('success') }}
            </div>
        </div>
    @endif

    {{-- Grid de anuncios --}}
    <div class="max-w-5xl mx-auto px-6">
        @if($anuncios->count() > 0)
            <div class="grid grid-cols-3 gap-5">
                @foreach($anuncios as $anuncio)
                    <a href="{{ route('segundamano.show', $anuncio->id) }}"
                       class="group block no-underline">

                        {{-- Imagen --}}
                        <div class="relative overflow-hidden bg-gray-900 aspect-[4/3] mb-3">
                            @if($anuncio->imagen)
                                <img src="{{ asset('storage/' . $anuncio->imagen) }}"
                                     alt=""
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-700 text-xs tracking-widest uppercase">
                                    Sin imagen
                                </div>
                            @endif

                            {{-- Badge estado --}}
                            <span class="absolute top-2 left-2 px-2 py-0.5 text-[10px] tracking-widest uppercase
                                         bg-black/70 border border-white/10 text-gray-300 rounded">
                                {{ $anuncio->etiquetaEstado() }}
                            </span>

                            {{-- Overlay hover --}}
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all duration-300"></div>
                        </div>

                        {{-- Info --}}
                        <div>
                            <h3 class="text-sm font-light tracking-wider mb-1 text-white truncate">
                                {{ $anuncio->titulo }}
                            </h3>
                            <p class="text-yellow-500 text-base font-light">
                                €{{ number_format($anuncio->precio, 2) }}
                            </p>
                            <div class="flex items-center justify-between mt-1">
                                <p class="text-gray-500 text-[10px] tracking-widest uppercase">
                                    {{ $anuncio->categoria ?? '—' }}
                                </p>
                                <p class="text-gray-600 text-[10px]">
                                    {{ $anuncio->vendedor->name }}
                                </p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Paginación --}}
            <div class="mt-10 flex justify-center gap-2 text-xs">
                {{ $anuncios->links() }}
            </div>
        @else
            <div class="text-center py-20">
                <p class="text-gray-600 text-lg tracking-wider">No hay anuncios disponibles</p>
                @auth
                    <a href="{{ route('segundamano.crear') }}"
                       class="inline-block mt-4 px-6 py-2 border border-yellow-500 text-yellow-500 text-xs tracking-widest
                              hover:bg-yellow-500 hover:text-black transition-all">
                        Sé el primero en publicar
                    </a>
                @endauth
            </div>
        @endif
    </div>

</main>

</body>
</html>
