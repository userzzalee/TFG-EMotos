document.addEventListener('DOMContentLoaded', function() {
    const productoId = document.getElementById('selector-puntuacion').dataset.productoId;
    const clave = 'estrellas_' + productoId;
    const puntos = document.querySelectorAll('.punto-selector');
    let valorActual = localStorage.getItem(clave) || 0;

    function actualizarPuntos(valor) {
        puntos.forEach(punto => {
            if (parseInt(punto.dataset.valor) <= valor) {
                punto.classList.remove('text-gray-600');
                punto.classList.add('text-yellow-500');
            } else {
                punto.classList.remove('text-yellow-500');
                punto.classList.add('text-gray-600');
            }
        });
    }

    actualizarPuntos(valorActual);

    puntos.forEach(punto => {
        punto.addEventListener('click', function() {
            const valor = parseInt(this.dataset.valor);
            localStorage.setItem(clave, valor);
            valorActual = valor;
            actualizarPuntos(valor);
        });
    });
});
