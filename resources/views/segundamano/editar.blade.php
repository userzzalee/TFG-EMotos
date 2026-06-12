<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Anuncio – Alyx</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="m-0 bg-black min-h-screen overflow-x-hidden">

@include('layouts.navigation')

<div class="fixed inset-0 bg-gradient-to-br from-gray-900 via-black to-gray-900 z-0 pointer-events-none"></div>

<main class="relative z-10 text-white pt-[75px] pb-20">
    <div class="max-w-2xl mx-auto px-4 sm:px-8">

        <p class="text-[10px] tracking-[0.3em] text-yellow-500 uppercase mb-2 text-center">Segunda Mano</p>
        <h1 class="text-xl sm:text-2xl font-light tracking-[0.2em] mb-6 text-center uppercase">Editar Anuncio</h1>

        @if($errors->any())
            <div class="bg-red-900/50 border border-red-500 text-red-400 px-4 sm:px-6 py-4 rounded-lg mb-8 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('segundamano.update', $anuncio->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs tracking-widest text-gray-400 mb-2 uppercase">Título</label>
                <input type="text" name="titulo" value="{{ old('titulo', $anuncio->titulo) }}" required
                       class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-yellow-500 focus:outline-none transition-all">
            </div>

            <div>
                <label class="block text-xs tracking-widest text-gray-400 mb-2 uppercase">Descripción</label>
                <textarea name="descripcion" rows="4"
                          class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-yellow-500 focus:outline-none transition-all resize-none">{{ old('descripcion', $anuncio->descripcion) }}</textarea>
            </div>

            <div>
                <label class="block text-xs tracking-widest text-gray-400 mb-2 uppercase">Precio (€)</label>
                <input type="number" name="precio" value="{{ old('precio', $anuncio->precio) }}" step="0.01" min="0" required
                       class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-yellow-500 focus:outline-none transition-all">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs tracking-widest text-gray-400 mb-2 uppercase">Categoría</label>
                    <select name="categoria"
                            class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-yellow-500 focus:outline-none transition-all">
                        <option value="">Sin categoría</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat }}" {{ old('categoria', $anuncio->categoria) === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs tracking-widest text-gray-400 mb-2 uppercase">Estado</label>
                    <select name="estado" required
                            class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-yellow-500 focus:outline-none transition-all">
                        @foreach($estados as $valor => $etiqueta)
                            <option value="{{ $valor }}" {{ old('estado', $anuncio->estado) === $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" name="vendido" id="vendido" value="1"
                       {{ old('vendido', $anuncio->vendido) ? 'checked' : '' }}
                       class="w-4 h-4 accent-yellow-500">
                <label for="vendido" class="text-xs tracking-widest text-gray-400 uppercase cursor-pointer">Marcar como vendido</label>
            </div>

            @if($anuncio->imagen)
                <div>
                    <p class="text-xs tracking-widest text-gray-500 uppercase mb-3">Imagen de portada actual</p>
                    <img src="{{ asset('storage/' . $anuncio->imagen) }}" alt="{{ $anuncio->titulo }}"
                         class="h-28 sm:h-32 object-cover rounded border border-gray-700">
                </div>
            @endif

            @if($anuncio->imagenes->isNotEmpty())
                <div>
                    <p class="text-xs tracking-widest text-gray-500 uppercase mb-3">Galería actual</p>
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 sm:gap-3">
                        @foreach($anuncio->imagenes as $img)
                            <label class="relative block cursor-pointer group">
                                <img src="{{ $img->url() }}" alt=""
                                     class="h-20 sm:h-24 w-full object-cover rounded border border-gray-700 peer-checked:opacity-40 transition-all">
                                <input type="checkbox" name="eliminar_imagenes[]" value="{{ $img->id }}"
                                       class="peer absolute top-2 left-2 accent-red-500">
                                <span class="absolute inset-0 rounded ring-2 ring-red-500 opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none"></span>
                                <span class="absolute bottom-1 right-1 text-[9px] uppercase tracking-widest text-red-400 opacity-0 peer-checked:opacity-100 bg-black/70 px-1 rounded">Borrar</span>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-gray-600 text-xs mt-2">Marca las imágenes que quieras eliminar.</p>
                </div>
            @endif

            <div>
                <label class="block text-xs tracking-widest text-gray-400 mb-2 uppercase">{{ $anuncio->imagen ? 'Cambiar portada' : 'Imagen de portada' }}</label>
                <div class="border-2 border-dashed border-gray-700 rounded-lg p-4 text-center hover:border-yellow-500 transition-all">
                    <input type="file" name="imagen" accept="image/jpeg,image/png,image/jpg,image/gif"
                           class="w-full text-gray-400 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-gray-800 file:text-yellow-500 file:cursor-pointer hover:file:bg-gray-700 transition-all">
                    <p class="text-gray-600 text-xs mt-2">Dejar vacío para conservar la portada actual</p>
                </div>
            </div>

            <div>
                <label class="block text-xs tracking-widest text-gray-400 mb-2 uppercase">Añadir imágenes a la galería</label>
                <div class="border-2 border-dashed border-gray-700 rounded-lg p-4 text-center hover:border-yellow-500 transition-all">
                    <input type="file" name="imagenes[]" multiple accept="image/jpeg,image/png,image/jpg,image/gif"
                           class="w-full text-gray-400 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-gray-800 file:text-yellow-500 file:cursor-pointer hover:file:bg-gray-700 transition-all">
                    <p class="text-gray-600 text-xs mt-2">JPEG, PNG · Máx 3 MB · Hasta 8 imágenes</p>
                </div>
                @error('imagenes.*') <p class="text-[11px] text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 sm:gap-4 pt-2">
                <a href="{{ route('segundamano.mis-anuncios') }}"
                   class="flex-1 bg-gray-800 text-white py-3 sm:py-4 rounded-lg text-center text-xs tracking-widest uppercase hover:bg-gray-700 transition-all">
                   Cancelar
                </a>
                <button type="submit"
                        class="flex-1 bg-yellow-500 text-black py-3 sm:py-4 rounded-lg text-xs tracking-widest uppercase font-medium hover:bg-yellow-400 transition-all">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</main>

</body>
</html>
