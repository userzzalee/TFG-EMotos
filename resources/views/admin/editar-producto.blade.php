<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Producto - Alyx</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="m-0 bg-black min-h-screen overflow-x-hidden">

@include('layouts.navigation')

<div class="fixed inset-0 bg-gradient-to-br from-gray-900 via-black to-gray-900 z-0 pointer-events-none"></div>

<main class="relative z-10 text-white pt-[75px] pb-20">
    
    <div class="max-w-2xl mx-auto px-8">
        <h1 class="text-2xl font-light tracking-[0.2em] mb-6 text-center">EDITAR PRODUCTO</h1>
        
        @if(session('success'))
            <div class="bg-green-900/50 border border-green-500 text-green-400 px-4 py-3 rounded-lg mb-6 text-center text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-900/50 border border-red-500 text-red-400 px-4 py-3 rounded-lg mb-6 text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('merchandising.update', $producto->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('POST')
            
            <!-- Nombre -->
            <div>
                <label class="block text-xs tracking-widest text-gray-400 mb-2">NOMBRE</label>
                <input type="text" name="nombre" value="{{ $producto->nombre }}" required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-yellow-500 focus:outline-none transition-all text-sm" placeholder="Nombre del producto">
            </div>

            <!-- Descripción -->
            <div>
                <label class="block text-xs tracking-widest text-gray-400 mb-2">DESCRIPCIÓN</label>
                <textarea name="descripcion" rows="3" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-yellow-500 focus:outline-none transition-all resize-none text-sm" placeholder="Descripción del producto">{{ $producto->descripcion ?? '' }}</textarea>
            </div>

            <!-- Precio -->
            <div>
                <label class="block text-xs tracking-widest text-gray-400 mb-2">PRECIO (€)</label>
                <input type="number" name="precio" step="0.01" min="0" value="{{ $producto->precio }}" required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-yellow-500 focus:outline-none transition-all text-sm" placeholder="0.00">
            </div>

            <!-- Stock -->
            <div>
                <label class="block text-xs tracking-widest text-gray-400 mb-2">STOCK</label>
                <input type="number" name="stock" min="0" value="{{ $producto->stock }}" required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-yellow-500 focus:outline-none transition-all text-sm" placeholder="0">
            </div>

            <!-- Categoría -->
            <div>
                <label class="block text-xs tracking-widest text-gray-400 mb-2">CATEGORÍA</label>
                <select name="categoria" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-yellow-500 focus:outline-none transition-all text-sm">
                    <option value="">Seleccionar categoría</option>
                    <option value="ropa" {{ $producto->categoria === 'ropa' ? 'selected' : '' }}>Ropa</option>
                    <option value="accesorios" {{ $producto->categoria === 'accesorios' ? 'selected' : '' }}>Accesorios</option>
                    <option value="cascos" {{ $producto->categoria === 'cascos' ? 'selected' : '' }}>Cascos</option>
                </select>
            </div>

            <!-- Imagen -->
            <div>
                <label class="block text-xs tracking-widest text-gray-400 mb-2">IMAGEN</label>
                @if($producto->imagen)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $producto->imagen) }}" alt="Imagen actual" class="w-24 h-24 object-cover rounded-lg border border-gray-700">
                    </div>
                @endif
                <div class="border-2 border-dashed border-gray-700 rounded-lg p-6 text-center hover:border-yellow-500 transition-all cursor-pointer">
                    <input type="file" name="imagen" accept="image/jpeg,image/png,image/jpg,image/gif" class="w-full text-gray-400 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-gray-800 file:text-yellow-500 file:cursor-pointer hover:file:bg-gray-700 transition-all text-xs">
                    <p class="text-gray-500 text-[10px] mt-3">Selecciona una nueva imagen para reemplazar la actual</p>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex gap-3 pt-3">
                <a href="{{ route('merchandising') }}" class="flex-1 bg-gray-800 text-white py-3 rounded-lg text-center tracking-widest hover:bg-gray-700 transition-all text-sm">CANCELAR</a>
                <button type="submit" class="flex-1 bg-yellow-500 text-black py-3 rounded-lg tracking-widest font-medium hover:bg-yellow-400 transition-all text-sm">ACTUALIZAR</button>
            </div>
        </form>
    </div>

</main>

</body>
</html>
