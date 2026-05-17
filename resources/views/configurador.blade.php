<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Configura tu Moto - Club Motos</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('js/configurador.js') }}" defer></script>
</head>
<body class="m-0 bg-black h-screen overflow-x-hidden">

@include('layouts.navigation')

<div class="fixed inset-0 bg-gradient-to-br from-gray-900 via-black to-gray-900 z-0 pointer-events-none"></div>

<main class="relative z-10 text-white h-screen flex">
    
    <!-- Panel izquierdo - Imagen grande -->
    <div class="w-2/3 h-full flex items-center justify-center p-0">
        <div id="moto-preview" class="w-full h-full flex items-center justify-center transition-all duration-500">
            <img id="moto-emoji" src="{{ asset('images/moto1.jpg') }}" alt="Moto" class="w-full h-full object-contain transition-all duration-300">
        </div>
    </div>
    
    <!-- Panel derecho - Opciones -->
    <div id="options-panel" class="w-1/3 h-full bg-black/80 backdrop-blur-xl border-l border-gray-800 overflow-hidden relative flex flex-col">
        
        <!-- Contenido de secciones -->
        <div class="flex-1 overflow-y-auto p-8 flex items-center">
            
            <!-- Sección Modelo -->
            <div id="section-modelo" class="category-section w-full">
                <h1 class="text-3xl font-semibold mb-2 text-yellow-500 text-center">Configura tu Moto</h1>
                <p class="text-gray-400 mb-8 text-center">Elige tu modelo</p>
                
                <div class="space-y-3">
                    <button onclick="selectModel('sport')" class="model-btn w-full bg-gray-800 text-white rounded-xl py-4 px-6 text-left hover:bg-gray-700 transition-all border border-gray-700" data-model="sport">
                        <span class="font-medium">Sport</span>
                        <span class="text-gray-400 text-sm block mt-1">Diseño aerodinámico</span>
                    </button>
                    <button onclick="selectModel('cruiser')" class="model-btn w-full bg-gray-800 text-white rounded-xl py-4 px-6 text-left hover:bg-gray-700 transition-all border border-gray-700" data-model="cruiser">
                        <span class="font-medium">Cruiser</span>
                        <span class="text-gray-400 text-sm block mt-1">Estilo clásico</span>
                    </button>
                </div>
            </div>
            
            <!-- Sección Color -->
            <div id="section-color" class="category-section hidden w-full">
                <h2 class="text-2xl font-semibold mb-2 text-yellow-500 text-center">Color</h2>
                <p class="text-gray-400 mb-8 text-center">Elige el color de tu moto</p>
                
                <div class="flex gap-4 justify-center">
                    <button onclick="selectColor('negro')" class="color-btn w-16 h-16 rounded-full bg-gray-900 hover:scale-110 transition-transform border-2 border-transparent hover:border-yellow-500" data-color="negro"></button>
                    <button onclick="selectColor('dorado')" class="color-btn w-16 h-16 rounded-full bg-yellow-500 hover:scale-110 transition-transform border-2 border-transparent hover:border-yellow-500" data-color="dorado"></button>
                    <button onclick="selectColor('gris')" class="color-btn w-16 h-16 rounded-full bg-gray-600 hover:scale-110 transition-transform border-2 border-transparent hover:border-yellow-500" data-color="gris"></button>
                    <button onclick="selectColor('blanco')" class="color-btn w-16 h-16 rounded-full bg-white hover:scale-110 transition-transform border-2 border-transparent hover:border-yellow-500" data-color="blanco"></button>
                </div>
            </div>
            
            <!-- Sección Motor -->
            <div id="section-motor" class="category-section hidden w-full">
                <h2 class="text-2xl font-semibold mb-2 text-yellow-500 text-center">Motor</h2>
                <p class="text-gray-400 mb-8 text-center">Elige la potencia</p>
                
                <div class="space-y-3">
                    <button onclick="selectEngine('250')" class="engine-btn w-full bg-gray-800 text-white rounded-xl py-4 px-6 text-left hover:bg-gray-700 transition-all border border-gray-700" data-engine="250">
                        <span class="font-medium">250cc</span>
                        <span class="text-gray-400 text-sm block mt-1">Estándar</span>
                    </button>
                    <button onclick="selectEngine('500')" class="engine-btn w-full bg-gray-800 text-white rounded-xl py-4 px-6 text-left hover:bg-gray-700 transition-all border border-gray-700" data-engine="500">
                        <span class="font-medium">500cc</span>
                        <span class="text-gray-400 text-sm block mt-1">Potencia media</span>
                    </button>
                    <button onclick="selectEngine('1000')" class="engine-btn w-full bg-gray-800 text-white rounded-xl py-4 px-6 text-left hover:bg-gray-700 transition-all border border-gray-700" data-engine="1000">
                        <span class="font-medium">1000cc</span>
                        <span class="text-gray-400 text-sm block mt-1">Alta potencia</span>
                    </button>
                </div>
            </div>
            
            <!-- Sección Resumen -->
            <div id="section-resumen" class="category-section hidden w-full">
                <h2 class="text-2xl font-semibold mb-2 text-yellow-500 text-center">Resumen</h2>
                <p class="text-gray-400 mb-8 text-center">Revisa tu configuración</p>
                
                <div class="space-y-4">
                    <div class="bg-gray-800 rounded-xl p-4">
                        <p class="text-gray-400 text-sm">Modelo</p>
                        <p id="summary-model" class="text-lg font-medium">Sport</p>
                    </div>
                    <div class="bg-gray-800 rounded-xl p-4">
                        <p class="text-gray-400 text-sm">Color</p>
                        <p id="summary-color" class="text-lg font-medium">Negro</p>
                    </div>
                    <div class="bg-gray-800 rounded-xl p-4">
                        <p class="text-gray-400 text-sm">Motor</p>
                        <p id="summary-engine" class="text-lg font-medium">250cc</p>
                    </div>
                </div>
                
                <div class="mt-8 pt-8 border-t border-gray-700">
                    <div class="flex justify-between items-center mb-6">
                        <span class="text-gray-400">Precio total</span>
                        <span id="total-price" class="text-3xl font-semibold text-yellow-500">€5.999</span>
                    </div>
                    <button class="w-full bg-yellow-500 text-black rounded-xl py-4 font-medium hover:bg-yellow-400 transition-all">
                        Añadir al carrito
                    </button>
                </div>
            </div>
            
        </div>
        
        <!-- Navegación inferior -->
        <div class="border-t border-gray-700 p-4 flex flex-col items-center gap-4 bg-black/90">
            <div class="flex gap-2">
                <span id="step-1" class="w-3 h-3 rounded-full bg-yellow-500"></span>
                <span id="step-2" class="w-3 h-3 rounded-full bg-gray-600"></span>
                <span id="step-3" class="w-3 h-3 rounded-full bg-gray-600"></span>
                <span id="step-4" class="w-3 h-3 rounded-full bg-gray-600"></span>
            </div>
            <div class="flex gap-4">
                <button onclick="prevSection()" id="prev-btn" class="px-6 py-3 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    ← Anterior
                </button>
                <button onclick="nextSection()" id="next-btn" class="px-6 py-3 bg-yellow-500 text-black rounded-lg hover:bg-yellow-400 transition-all font-medium">
                    Siguiente →
                </button>
            </div>
        </div>
        
    </div>
    
</main>

</body>
</html>
