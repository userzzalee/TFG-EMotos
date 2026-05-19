document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.boton-filtro');
    const productCards = document.querySelectorAll('.tarjeta-producto');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Quitar color dorado de todos los botones
            filterBtns.forEach(b => b.classList.remove('border-yellow-500', 'text-yellow-500'));
            // Poner color dorado al botón clicado
            this.classList.add('border-yellow-500', 'text-yellow-500');

            const filter = this.dataset.filter;

            productCards.forEach(card => {
                if (filter === 'all' || card.dataset.category === filter) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // Activar primer filtro por defecto
    filterBtns[0].classList.add('border-yellow-500', 'text-yellow-500');
});
