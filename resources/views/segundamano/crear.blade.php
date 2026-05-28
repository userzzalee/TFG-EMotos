<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Publicar Anuncio – Alyx</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="m-0 bg-black min-h-screen overflow-x-hidden">

@include('layouts.navigation')

<div class="fixed inset-0 bg-gradient-to-br from-gray-900 via-black to-gray-900 z-0 pointer-events-none"></div>

<main class="relative z-10 text-white pt-[75px] pb-20">
    <div class="max-w-2xl mx-auto px-8">

        <p class="text-[10px] tracking-[0.3em] text-yellow-500 uppercase mb-2 text-center">Segunda Mano</p>
        <h1 class="text-2xl font-light tracking-[0.2em] mb-6 text-center uppercase">Nuevo Anuncio</h1>

        @if($errors->any())
            <div class="bg-red-900/50 border border-red-500 text-red-400 px-6 py-4 rounded-lg mb-8 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('segundamano.store') }}" method="POST"
              enctype="multipart/form-data" class="space-y-4">
            @csrf

            {{-- Título --}}
            <div>
                <label class="block text-xs tracking-widest text-gray-400 mb-2 uppercase">Título</label>
                <input type="text" name="titulo" value="{{ old('titulo') }}" required
                       class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white text-sm
                              focus:border-yellow-500 focus:outline-none transition-all"
                       placeholder="Ej: Casco Shoei GT-Air talla M">
            </div>

            {{-- Descripción --}}
            <div>
                <label class="block text-xs tracking-widest text-gray-400 mb-2 uppercase">Descripción</label>
                <textarea name="descripcion" rows="3"
                          class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white text-sm
                                 focus:border-yellow-500 focus:outline-none transition-all resize-none"
                          placeholder="Describe el artículo, su uso, posibles defectos…">{{ old('descripcion') }}</textarea>
            </div>

            {{-- Precio --}}
            <div>
                <label class="block text-xs tracking-widest text-gray-400 mb-2 uppercase">Precio (€)</label>
                <input type="number" name="precio" value="{{ old('precio') }}" step="0.01" min="0" required
                       class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white text-sm
                              focus:border-yellow-500 focus:outline-none transition-all"
                       placeholder="0.00">
            </div>

            {{-- Categoría + Estado en fila --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs tracking-widest text-gray-400 mb-2 uppercase">Categoría</label>
                    <select name="categoria"
                            class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white text-sm
                                   focus:border-yellow-500 focus:outline-none transition-all">
                        <option value="">Sin categoría</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat }}" {{ old('categoria') === $cat ? 'selected' : '' }}>
                                {{ ucfirst($cat) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs tracking-widest text-gray-400 mb-2 uppercase">Estado</label>
                    <select name="estado" required
                            class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white text-sm
                                   focus:border-yellow-500 focus:outline-none transition-all">
                        @foreach($estados as $valor => $etiqueta)
                            <option value="{{ $valor }}" {{ old('estado') === $valor ? 'selected' : '' }}>
                                {{ $etiqueta }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Imagen --}}
            <div>
                <label class="block text-xs tracking-widest text-gray-400 mb-2 uppercase">Imagen</label>
                <div class="border-2 border-dashed border-gray-700 rounded-lg p-4 text-center hover:border-yellow-500 transition-all">
                    <input type="file" name="imagen" accept="image/jpeg,image/png,image/jpg,image/gif"
                           class="w-full text-gray-400 text-sm
                                  file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0
                                  file:bg-gray-800 file:text-yellow-500 file:cursor-pointer
                                  hover:file:bg-gray-700 transition-all">
                    <p class="text-gray-600 text-xs mt-2">JPEG, PNG, GIF · Máx 3 MB · Opcional</p>
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex gap-3 pt-2">
                <a href="{{ route('segundamano.index') }}"
                   class="flex-1 bg-gray-800 text-white py-2 rounded-lg text-center text-xs tracking-widest uppercase
                          hover:bg-gray-700 transition-all">
                    Cancelar
                </a>
                <button type="submit"
                        class="flex-1 bg-yellow-500 text-black py-2 rounded-lg text-xs tracking-widest uppercase
                               font-medium hover:bg-yellow-400 transition-all">
                    Publicar
                </button>
            </div>
        </form>
    </div>
</main>

</body>
</html>
