let modeloSeleccionado = 'enduro';
let colorSeleccionado = 'gris';
let motorSeleccionado = '40';

const precios = {
    enduro: { base: 4999 },
    trail: { base: 5999 }
};

const preciosMotor = {
    '40': 0,
    '80': 1500
};

const nombresColores = {
    'gris': 'Gris',
    'dorado': 'Dorado',
    'rojo': 'Rojo'
};

function seleccionarModelo(modelo) {
    modeloSeleccionado = modelo;

    const resumenModelo = document.getElementById('summary-model');
    if (resumenModelo) {
        resumenModelo.textContent = modelo.charAt(0).toUpperCase() + modelo.slice(1);
    }

    document.querySelectorAll('.models').forEach(btn => {
        btn.classList.remove('bg-yellow-500', 'border-yellow-500', 'text-black');
        btn.classList.add('bg-gray-800', 'border-gray-700', 'text-white');
    });

    const btnSeleccionado = document.querySelector(`[data-model="${modelo}"]`);
    if (btnSeleccionado) {
        btnSeleccionado.classList.remove('bg-gray-800', 'border-gray-700', 'text-white');
        btnSeleccionado.classList.add('bg-yellow-500', 'border-yellow-500', 'text-black');
    }

    mostrarMensajeDesarrollo();
    actualizarPrecio();
    actualizarCamposOcultos();
    actualizarImagenMoto();
}

function seleccionarColor(color) {
    colorSeleccionado = color;

    const resumenColor = document.getElementById('summary-color');
    if (resumenColor) {
        resumenColor.textContent = nombresColores[color] || color;
    }

    const etiquetaColor = document.getElementById('color-label');
    if (etiquetaColor) {
        etiquetaColor.textContent = nombresColores[color] || color;
    }

    document.querySelectorAll('.colors').forEach(btn => {
        btn.classList.remove('border-yellow-500', 'scale-110');
    });

    const btnSeleccionado = document.querySelector(`[data-color="${color}"]`);
    if (btnSeleccionado) {
        btnSeleccionado.classList.add('border-yellow-500', 'scale-110');
    }

    actualizarPrecio();
    actualizarCamposOcultos();
    actualizarImagenMoto();
}

function seleccionarMotor(motor) {
    motorSeleccionado = motor;

    const resumenMotor = document.getElementById('summary-engine');
    if (resumenMotor) {
        resumenMotor.textContent = motor + ' HP';
    }

    mostrarMensajeDesarrollo();

    document.querySelectorAll('.engines').forEach(btn => {
        btn.classList.remove('bg-yellow-500', 'border-yellow-500', 'text-black');
        btn.classList.add('bg-gray-800', 'border-gray-700', 'text-white');
    });

    const btnSeleccionado = document.querySelector(`[data-engine="${motor}"]`);
    if (btnSeleccionado) {
        btnSeleccionado.classList.remove('bg-gray-800', 'border-gray-700', 'text-white');
        btnSeleccionado.classList.add('bg-yellow-500', 'border-yellow-500', 'text-black');
    }

    actualizarPrecio();
    actualizarCamposOcultos();
    actualizarImagenMoto();
}

function mostrarMensajeDesarrollo() {
    let mensaje = document.getElementById('mensaje-desarrollo');
    const imagenMoto = document.getElementById('moto-emoji');
    const contenedor = document.getElementById('moto-preview');
    const precioTotalEl = document.getElementById('total-price');
    const btnComprar = document.querySelector('#add-to-cart-form button[type="submit"]');

    if (modeloSeleccionado === 'trail' && motorSeleccionado === '80') {
        if (imagenMoto) imagenMoto.style.display = 'none';
        if (!mensaje && contenedor) {
            mensaje = document.createElement('p');
            mensaje.id = 'mensaje-desarrollo';
            mensaje.className = 'text-2xl font-semibold text-yellow-500/50 tracking-widest';
            mensaje.textContent = 'En desarrollo';
            contenedor.appendChild(mensaje);
        }
        if (precioTotalEl) precioTotalEl.textContent = '???';
        if (btnComprar) {
            btnComprar.disabled = true;
            btnComprar.classList.remove('bg-yellow-500', 'hover:bg-yellow-400');
            btnComprar.classList.add('bg-gray-600', 'cursor-not-allowed');
        }
    } else {
        if (imagenMoto) imagenMoto.style.display = '';
        if (mensaje) mensaje.remove();
        if (btnComprar) {
            btnComprar.disabled = false;
            btnComprar.classList.remove('bg-gray-600', 'cursor-not-allowed');
            btnComprar.classList.add('bg-yellow-500', 'hover:bg-yellow-400');
        }
    }
}

function actualizarImagenMoto() {
    const imagenMoto = document.getElementById('moto-emoji');
    if (!imagenMoto) return;

    const imagenes = {
        'enduro': {
            '40': { 'gris': 'images/40hpgrispng.png', 'dorado': 'images/40hpamarillapng.png', 'rojo': 'images/40hprojopng.png' },
            '80': { 'gris': 'images/80hpgrispng.png', 'dorado': 'images/80hpamarillapng.png', 'rojo': 'images/80hprojapng.png' }
        },
        'trail': {
            '40': { 'gris': 'images/trail80hpnegra.png', 'dorado': 'images/80hptrailpng.png', 'rojo': 'images/trail80hproja.png' },
            '80': { 'gris': 'images/trail80hpnegra.png', 'dorado': 'images/80hptrailpng.png', 'rojo': 'images/trail80hproja.png' }
        }
    };

    const ruta = imagenes[modeloSeleccionado]?.[motorSeleccionado]?.[colorSeleccionado] || 'images/40hpgrispng.png';
    imagenMoto.src = ruta;
}

function actualizarPrecio() {
    const precioTotalEl = document.getElementById('total-price');
    if (modeloSeleccionado === 'trail' && motorSeleccionado === '80') {
        if (precioTotalEl) precioTotalEl.textContent = '???';
        return;
    }

    const precioBase = precios[modeloSeleccionado].base;
    const precioMotor = preciosMotor[motorSeleccionado];
    const total = precioBase + precioMotor;

    if (precioTotalEl) {
        precioTotalEl.textContent = '€' + total.toLocaleString();
    }
}

function actualizarCamposOcultos() {
    const precioBase = precios[modeloSeleccionado].base;
    const precioMotor = preciosMotor[motorSeleccionado];
    const total = precioBase + precioMotor;

    const campoModelo = document.getElementById('config-modelo');
    const campoColor = document.getElementById('config-color');
    const campoMotor = document.getElementById('config-motor');
    const campoPrecio = document.getElementById('config-precio');

    if (campoModelo) campoModelo.value = modeloSeleccionado;
    if (campoColor) campoColor.value = colorSeleccionado;
    if (campoMotor) campoMotor.value = motorSeleccionado;
    if (campoPrecio) campoPrecio.value = total;
}

document.addEventListener('DOMContentLoaded', function() {
    seleccionarModelo('enduro');
    seleccionarColor('gris');
    seleccionarMotor('40');
});
