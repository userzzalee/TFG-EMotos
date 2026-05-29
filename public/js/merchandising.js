document.addEventListener('DOMContentLoaded', function() {
    const btnFiltrado = document.querySelectorAll('.boton-filtro');
    const cardsproducto = document.querySelectorAll('.tarjeta-producto');

    btnFiltrado.forEach(btn => {
        btn.addEventListener('click', function() {
            btnFiltrado.forEach(b => b.classList.remove('border-yellow-500', 'text-yellow-500'));
            this.classList.add('border-yellow-500', 'text-yellow-500');

            const filtro = this.dataset.filter;

            cardsproducto.forEach(card => {
                if (filtro === 'all' || card.dataset.category === filtro) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    btnFiltrado[0].classList.add('border-yellow-500', 'text-yellow-500');

    // Sistema de estrellas con localStorage
    inicializarEstrellas();
});

function inicializarEstrellas() {
    const contenedores = document.querySelectorAll('.estrellas');

    contenedores.forEach(contenedor => {
        const productoId = contenedor.dataset.productoId;
        const clave = 'estrellas_' + productoId;
        let valoracion = localStorage.getItem(clave);

        if (!valoracion) {
            valoracion = Math.floor(Math.random() * 3) + 3;
            localStorage.setItem(clave, valoracion);
        }

        valoracion = parseInt(valoracion);
        renderizarEstrellas(contenedor, valoracion, productoId);
    });
}

function renderizarEstrellas(contenedor, valoracion) {
    contenedor.innerHTML = '';

    for (let i = 1; i <= 5; i++) {
        const punto = document.createElement('span');
        punto.textContent = '●';
        punto.classList.add('text-sm');

        if (i <= valoracion) {
            punto.classList.add('text-yellow-500');
        } else {
            punto.classList.add('text-gray-600');
        }

        contenedor.appendChild(punto);
    }
}
