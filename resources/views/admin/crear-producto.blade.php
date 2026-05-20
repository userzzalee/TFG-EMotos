<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crear Producto - Alyx</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="m-0 bg-black min-h-screen overflow-x-hidden">

@include('layouts.navigation')

<div class="fixed inset-0 bg-gradient-to-br from-gray-900 via-black to-gray-900 z-0 pointer-events-none"></div>

<main class="relative z-10 text-white pt-[100px] pb-20">
    
    <div class="max-w-2xl mx-auto px-8">
        <h1 class="text-4xl font-light tracking-[0.3em] mb-8 text-center">NUEVO PRODUCTO</h1>
        
        @if(session('success'))
            <div class="bg-green-900/50 border border-green-500 text-green-400 px-6 py-4 rounded-lg mb-8 text-center">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-900/50 border border-red-500 text-red-400 px-6 py-4 rounded-lg mb-8">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('merchandising.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            
            <!-- Nombre -->
            <div>
                <label class="block text-sm tracking-widest text-gray-400 mb-3">NOMBRE</label>
                <input type="text" name="nombre" required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-6 py-4 text-white focus:border-yellow-500 focus:outline-none transition-all" placeholder="Nombre del producto">
            </div>

            <!-- Descripción -->
            <div>
                <label class="block text-sm tracking-widest text-gray-400 mb-3">DESCRIPCIÓN</label>
                <textarea name="descripcion" rows="4" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-6 py-4 text-white focus:border-yellow-500 focus:outline-none transition-all resize-none" placeholder="Descripción del producto"></textarea>
            </div>

            <!-- Precio -->
            <div>
                <label class="block text-sm tracking-widest text-gray-400 mb-3">PRECIO (€)</label>
                <input type="number" name="precio" step="0.01" min="0" required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-6 py-4 text-white focus:border-yellow-500 focus:outline-none transition-all" placeholder="0.00">
            </div>

            <!-- Stock -->
            <div>
                <label class="block text-sm tracking-widest text-gray-400 mb-3">STOCK</label>
                <input type="number" name="stock" min="0" required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-6 py-4 text-white focus:border-yellow-500 focus:outline-none transition-all" placeholder="0">
            </div>

            <!-- Categoría -->
            <div>
                <label class="block text-sm tracking-widest text-gray-400 mb-3">CATEGORÍA</label>
                <select name="categoria" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-6 py-4 text-white focus:border-yellow-500 focus:outline-none transition-all">
                    <option value="">Seleccionar categoría</option>
                    <option value="ropa">Ropa</option>
                    <option value="accesorios">Accesorios</option>
                    <option value="cascos">Cascos</option>
                </select>
            </div>

            <!-- Imagen -->
            <div>
                <label class="block text-sm tracking-widest text-gray-400 mb-3">IMAGEN</label>
                <div class="border-2 border-dashed border-gray-700 rounded-lg p-8 text-center hover:border-yellow-500 transition-all cursor-pointer">
                    <input type="file" name="imagen" accept="image/jpeg,image/png,image/jpg,image/gif" required class="w-full text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-gray-800 file:text-yellow-500 file:cursor-pointer hover:file:bg-gray-700 transition-all">
                    <p class="text-gray-500 text-sm mt-4">Formatos: JPEG, PNG, JPG, GIF (Máx 2MB)</p>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex gap-4 pt-4">
                <a href="{{ route('merchandising') }}" class="flex-1 bg-gray-800 text-white py-4 rounded-lg text-center tracking-widest hover:bg-gray-700 transition-all">CANCELAR</a>
                <button type="submit" class="flex-1 bg-yellow-500 text-black py-4 rounded-lg tracking-widest font-medium hover:bg-yellow-400 transition-all">GUARDAR</button>
            </div>
        </form>
    </div>

</main>

</body>
</html>
