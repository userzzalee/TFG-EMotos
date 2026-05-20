document.addEventListener('DOMContentLoaded', function() {
    const btnFiltrado = document.querySelectorAll('.boton-filtro');
    const cardsproducto = document.querySelectorAll('.tarjeta-producto');

    btnFiltrado.forEach(btn => {
        btn.addEventListener('click', function() {
            // Quitar color dorado de todos los botones
            btnFiltrado.forEach(b => b.classList.remove('border-yellow-500', 'text-yellow-500'));
            // Poner color dorado al botón clicado
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

    // Activar primer filtro por defecto
    btnFiltrado[0].classList.add('border-yellow-500', 'text-yellow-500');
});
