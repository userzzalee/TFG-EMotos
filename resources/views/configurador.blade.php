<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Configura tu Moto - Alyx</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('js/configurador.js') }}" defer></script>
</head>
<body class="m-0 bg-black overflow-x-hidden">

@include('layouts.navigation')

<div class="fixed inset-0 bg-gradient-to-br from-gray-900 via-black to-gray-900 z-0 pointer-events-none"></div>

<main class="relative z-10 text-white flex">
    
    <!--Panel imagen (fijo a la izquierda)-->
    <div class="w-2/3 h-screen sticky top-0 flex items-center justify-center">
        <div id="moto-preview" class="w-full h-full flex items-center justify-center transition-all duration-500">
            <img id="moto-emoji" src="{{ asset('images/40hpgrispng.png') }}" alt="Moto" class="w-[82%] h-[82%] object-contain transition-all duration-300">
        </div>
    </div>
    
    <!-- Panel derecho (scroll continuo) -->
    <div id="options-panel" class="w-1/3 bg-black/80 backdrop-blur-xl border-l border-gray-800">
        
        <!-- Cabecera -->
        <div class="pt-[80px] pb-6 px-6 text-center border-b border-gray-800">
            <h1 class="text-xl font-semibold text-yellow-500">Configura tu Moto</h1>
            <p class="text-gray-400 text-sm mt-1">Personaliza cada detalle</p>
        </div>

        <!-- Seccion Modelo -->
        <div class="px-6 py-24 border-b border-gray-800">
            <p class="text-[10px] tracking-[0.3em] text-yellow-500 uppercase mb-1">Paso 1</p>
            <h2 class="text-lg font-semibold mb-1">Modelo</h2>
            <p class="text-gray-400 text-sm mb-5">Elige tu modelo base</p>
            <div class="space-y-2">
                <button onclick="seleccionarModelo('enduro')" class="models w-full bg-gray-800 text-white rounded-lg py-3 px-4 text-left hover:bg-gray-700 transition-all border border-gray-700" data-model="enduro">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="font-medium text-sm">Enduro</span>
                            <span class="text-gray-400 text-xs block mt-0.5">Todoterreno</span>
                        </div>
                        <span class="text-yellow-500 text-sm font-medium">€4.999</span>
                    </div>
                </button>
                <button onclick="seleccionarModelo('trail')" class="models w-full bg-gray-800 text-white rounded-lg py-3 px-4 text-left hover:bg-gray-700 transition-all border border-gray-700" data-model="trail">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="font-medium text-sm">Trail</span>
                            <span class="text-gray-400 text-xs block mt-0.5">Aventura mixta</span>
                        </div>
                        <span class="text-yellow-500 text-sm font-medium">€5.999</span>
                    </div>
                </button>
            </div>
        </div>

        <!-- Seccion Motor -->
        <div class="px-6 py-24 border-b border-gray-800">
            <p class="text-[10px] tracking-[0.3em] text-yellow-500 uppercase mb-1">Paso 2</p>
            <h2 class="text-lg font-semibold mb-1">Potencia</h2>
            <p class="text-gray-400 text-sm mb-5">Elige la potencia</p>
            <div class="space-y-2">
                <button onclick="seleccionarMotor('40')" class="engines w-full bg-gray-800 text-white rounded-lg py-3 px-4 text-left hover:bg-gray-700 transition-all border border-gray-700" data-engine="40">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="font-medium text-sm">40 HP</span>
                            <span class="text-gray-400 text-xs block mt-0.5">Estándar</span>
                        </div>
                        <span class="text-gray-500 text-sm">Incluido</span>
                    </div>
                </button>
                <button onclick="seleccionarMotor('80')" class="engines w-full bg-gray-800 text-white rounded-lg py-3 px-4 text-left hover:bg-gray-700 transition-all border border-gray-700" data-engine="80">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="font-medium text-sm">80 HP</span>
                            <span class="text-gray-400 text-xs block mt-0.5">Alta potencia</span>
                        </div>
                        <span class="text-yellow-500 text-sm font-medium">+€1.500</span>
                    </div>
                </button>
            </div>
        </div>

        <!-- Seccion Color -->
        <div class="px-6 py-24 border-b border-gray-800">
            <p class="text-[10px] tracking-[0.3em] text-yellow-500 uppercase mb-1">Paso 3</p>
            <h2 class="text-lg font-semibold mb-1">Color</h2>
            <p class="text-gray-400 text-sm mb-5">Elige el acabado</p>
            <div class="flex gap-4 justify-center">
                <button onclick="seleccionarColor('gris')" class="colors w-12 h-12 rounded-full bg-gray-500 hover:scale-110 transition-transform border-2 border-transparent hover:border-yellow-500" data-color="gris"></button>
                <button onclick="seleccionarColor('dorado')" class="colors w-12 h-12 rounded-full bg-yellow-500 hover:scale-110 transition-transform border-2 border-transparent hover:border-yellow-500" data-color="dorado"></button>
                <button onclick="seleccionarColor('rojo')" class="colors w-12 h-12 rounded-full bg-red-600 hover:scale-110 transition-transform border-2 border-transparent hover:border-yellow-500" data-color="rojo"></button>
            </div>
            <p id="color-label" class="text-center text-sm text-gray-400 mt-3">Gris</p>
        </div>

        <!-- Resumen -->
        <div class="px-6 py-8">
            <p class="text-[10px] tracking-[0.3em] text-yellow-500 uppercase mb-1">Resumen</p>
            <h2 class="text-lg font-semibold mb-4">Tu configuración</h2>
            <div class="space-y-2 mb-6">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Modelo</span>
                    <span id="summary-model" class="font-medium">Enduro</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Color</span>
                    <span id="summary-color" class="font-medium">Gris</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Motor</span>
                    <span id="summary-engine" class="font-medium">40 HP</span>
                </div>
            </div>
            <div class="pt-4 border-t border-gray-700">
                <div class="flex justify-between items-center mb-5">
                    <span class="text-gray-400 text-sm">Precio total</span>
                    <span id="total-price" class="text-2xl font-semibold text-yellow-500">€4.999</span>
                </div>
                <form id="add-to-cart-form" action="{{ route('cart.add-config') }}" method="POST">
                    @csrf
                    <input type="hidden" name="modelo" id="config-modelo" value="enduro">
                    <input type="hidden" name="color" id="config-color" value="gris">
                    <input type="hidden" name="motor" id="config-motor" value="40">
                    <input type="hidden" name="precio" id="config-precio" value="4999">
                    <button type="submit" class="w-full bg-yellow-500 text-black rounded-lg py-3 font-medium hover:bg-yellow-400 transition-all text-sm" onclick="this.disabled=true;this.form.submit();">Añadir al carrito</button>
                </form>
            </div>
        </div>

    </div>
    
</main>

</body>
</html>
