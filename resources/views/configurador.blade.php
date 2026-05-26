<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Configura tu Moto - Alyx</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('js/configurador.js') }}" defer></script>
</head>
<body class="m-0 bg-black h-screen overflow-x-hidden">

@include('layouts.navigation')

<div class="fixed inset-0 bg-gradient-to-br from-gray-900 via-black to-gray-900 z-0 pointer-events-none"></div>

<main class="relative z-10 text-white h-screen flex">
    
    <!--Panel imagen-->
    <div class="w-2/3 h-full flex items-center justify-center p-0">
        <div id="moto-preview" class="w-full h-full flex items-center justify-center transition-all duration-500">
            <img id="moto-emoji" src="{{ asset('images/moto1.jpg') }}" alt="Moto" class="w-full h-full object-contain transition-all duration-300">
        </div>
    </div>
    
    <!-- Panel derecho-->
    <div id="options-panel" class="w-1/3 h-full bg-black/80 backdrop-blur-xl border-l border-gray-800 overflow-hidden relative flex flex-col">
        
        <!-- Contenido de tipo motores -->
        <div class="flex-1 overflow-y-auto p-5 flex items-center">
            
            <!-- Seccion de los modelo -->
            <div id="section-modelo" class="tipomotos w-full">
                <h1 class="text-xl font-semibold mb-1 text-yellow-500 text-center">Configura tu Moto</h1>
                <p class="text-gray-400 mb-5 text-center text-sm">Elige tu modelo</p>
                
                <div class="space-y-2">
                    <button onclick="selectModel('sport')" class="modelos w-full bg-gray-800 text-white rounded-lg py-3 px-4 text-left hover:bg-gray-700 transition-all border border-gray-700" data-model="sport">
                        <span class="font-medium text-sm">Sport</span>
                        <span class="text-gray-400 text-xs block mt-0.5">Diseño aerodinámico</span>
                    </button>
                    <button onclick="selectModel('cruiser')" class="modelos w-full bg-gray-800 text-white rounded-lg py-3 px-4 text-left hover:bg-gray-700 transition-all border border-gray-700" data-model="cruiser">
                        <span class="font-medium text-sm">Cruiser</span>
                        <span class="text-gray-400 text-xs block mt-0.5">Estilo clásico</span>
                    </button>
                </div>
            </div>
            
            <!-- Seccion del motor -->
            <div id="section-motor" class="tipomotos hidden w-full">
                <h2 class="text-lg font-semibold mb-1 text-yellow-500 text-center">Motor</h2>
                <p class="text-gray-400 mb-5 text-center text-sm">Elige la potencia</p>
                
                <div class="space-y-2">
                    <button onclick="selectEngine('250')" class="tiposmotores w-full bg-gray-800 text-white rounded-lg py-3 px-4 text-left hover:bg-gray-700 transition-all border border-gray-700" data-engine="250">
                        <span class="font-medium text-sm">250cc</span>
                        <span class="text-gray-400 text-xs block mt-0.5">Estándar</span>
                    </button>
                    <button onclick="selectEngine('500')" class="tiposmotores w-full bg-gray-800 text-white rounded-lg py-3 px-4 text-left hover:bg-gray-700 transition-all border border-gray-700" data-engine="500">
                        <span class="font-medium text-sm">500cc</span>
                        <span class="text-gray-400 text-xs block mt-0.5">Potencia media</span>
                    </button>
                    <button onclick="selectEngine('1000')" class="tiposmotores w-full bg-gray-800 text-white rounded-lg py-3 px-4 text-left hover:bg-gray-700 transition-all border border-gray-700" data-engine="1000">
                        <span class="font-medium text-sm">1000cc</span>
                        <span class="text-gray-400 text-xs block mt-0.5">Alta potencia</span>
                    </button>
                </div>
            </div>
            
            <!-- Seccion de los colores -->
            <div id="section-color" class="tipomotos hidden w-full">
                <h2 class="text-lg font-semibold mb-1 text-yellow-500 text-center">Color</h2>
                <p class="text-gray-400 mb-5 text-center text-sm">Elige el color de tu moto</p>
                
                <div class="flex gap-3 justify-center">
                    <button onclick="selectColor('negro')" class="colores w-12 h-12 rounded-full bg-gray-900 hover:scale-110 transition-transform border-2 border-transparent hover:border-yellow-500" data-color="negro"></button>
                    <button onclick="selectColor('dorado')" class="colores w-12 h-12 rounded-full bg-yellow-500 hover:scale-110 transition-transform border-2 border-transparent hover:border-yellow-500" data-color="dorado"></button>
                    <button onclick="selectColor('rojo')" class="colores w-12 h-12 rounded-full bg-red-600 hover:scale-110 transition-transform border-2 border-transparent hover:border-yellow-500" data-color="rojo"></button>
                    <button onclick="selectColor('blanco')" class="colores w-12 h-12 rounded-full bg-white hover:scale-110 transition-transform border-2 border-transparent hover:border-yellow-500" data-color="blanco"></button>
                </div>
            </div>
            
            <!-- Seccion Resumen -->
            <div id="section-resumen" class="tipomotos hidden w-full">
                <h2 class="text-lg font-semibold mb-1 text-yellow-500 text-center">Resumen</h2>
                <p class="text-gray-400 mb-5 text-center text-sm">Revisa tu configuración</p>
                
                <div class="space-y-3">
                    <div class="bg-gray-800 rounded-lg p-3">
                        <p class="text-gray-400 text-xs">Modelo</p>
                        <p id="summary-model" class="text-base font-medium">Sport</p>
                    </div>
                    <div class="bg-gray-800 rounded-lg p-3">
                        <p class="text-gray-400 text-xs">Color</p>
                        <p id="summary-color" class="text-base font-medium">Negro</p>
                    </div>
                    <div class="bg-gray-800 rounded-lg p-3">
                        <p class="text-gray-400 text-xs">Motor</p>
                        <p id="summary-engine" class="text-base font-medium">250cc</p>
                    </div>
                </div>
                
                <div class="mt-5 pt-5 border-t border-gray-700">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-gray-400 text-sm">Precio total</span>
                        <span id="total-price" class="text-xl font-semibold text-yellow-500">€5.999</span>
                    </div>
                    <form id="add-to-cart-form" action="{{ route('cart.add-config') }}" method="POST">
                        @csrf
                        <input type="hidden" name="modelo" id="config-modelo" value="sport">
                        <input type="hidden" name="color" id="config-color" value="negro">
                        <input type="hidden" name="motor" id="config-motor" value="250">
                        <input type="hidden" name="precio" id="config-precio" value="5999">
                        <button type="submit" class="w-full bg-yellow-500 text-black rounded-lg py-3 font-medium hover:bg-yellow-400 transition-all text-sm">
                            Añadir al carrito
                        </button>
                    </form>
                </div>
            </div>
            
        </div>
        
        <!-- Navegacion de abajo-->
        <div class="border-t border-gray-700 p-3 flex flex-col items-center gap-3 bg-black/90">
            <div class="flex gap-2">
                <span id="step-1" class="w-2.5 h-2.5 rounded-full bg-yellow-500"></span>
                <span id="step-2" class="w-2.5 h-2.5 rounded-full bg-gray-600"></span>
                <span id="step-3" class="w-2.5 h-2.5 rounded-full bg-gray-600"></span>
                <span id="step-4" class="w-2.5 h-2.5 rounded-full bg-gray-600"></span>
            </div>
            <div class="flex gap-3">
                <button onclick="prevSection()" id="prev-btn" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed text-xs" disabled>
                    ← Anterior
                </button>
                <button onclick="nextSection()" id="next-btn" class="px-4 py-2 bg-yellow-500 text-black rounded-lg hover:bg-yellow-400 transition-all font-medium text-xs">
                    Siguiente →
                </button>
            </div>
        </div>
        
    </div>
    
</main>

</body>
</html>
