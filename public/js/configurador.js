let modeloSeleccionado = 'sport';
let colorSeleccionado = 'negro';
let motorSeleccionado = '250';
let seccionActual = 'modelo';

const secciones = ['modelo', 'color', 'motor', 'resumen'];
const precios = {
    sport: { base: 4999 },
    cruiser: { base: 5999 }
};

const preciosMotores = {
    '250': 0,
    '500': 1000,
    '1000': 2500
};

const nombresColores = {
    'negro': 'Negro',
    'dorado': 'Dorado',
    'gris': 'Gris',
    'blanco': 'Blanco'
};

let touchStartY = 0;
let touchEndY = 0;

function selectModel(model) {
    modeloSeleccionado = model;
    
    const motoName = document.getElementById('moto-name');
    const summaryModel = document.getElementById('summary-model');
    
    if (motoName) {
        motoName.textContent = model.charAt(0).toUpperCase() + model.slice(1);
    }
    if (summaryModel) {
        summaryModel.textContent = model.charAt(0).toUpperCase() + model.slice(1);
    }
    
    // Update button styles
    document.querySelectorAll('.modelos').forEach(btn => {
        btn.classList.remove('bg-yellow-500', 'border-yellow-500', 'text-black');
        btn.classList.add('bg-gray-800', 'border-gray-700', 'text-white');
    });
    
    const selectedBtn = document.querySelector(`[data-model="${model}"]`);
    if (selectedBtn) {
        selectedBtn.classList.remove('bg-gray-800', 'border-gray-700', 'text-white');
        selectedBtn.classList.add('bg-yellow-500', 'border-yellow-500', 'text-black');
    }
    
    updatePrice();
}

function selectColor(color) {
    colorSeleccionado = color;
    
    const motoColorName = document.getElementById('moto-color-name');
    const summaryColor = document.getElementById('summary-color');
    
    if (motoColorName) {
        motoColorName.textContent = nombresColores[color];
    }
    if (summaryColor) {
        summaryColor.textContent = nombresColores[color];
    }
    
    // Update button styles
    document.querySelectorAll('.colores').forEach(btn => {
        btn.classList.remove('border-yellow-500', 'scale-110');
    });
    
    const selectedBtn = document.querySelector(`[data-color="${color}"]`);
    if (selectedBtn) {
        selectedBtn.classList.add('border-yellow-500', 'scale-110');
    }
    
    updatePrice();
}

function selectEngine(engine) {
    motorSeleccionado = engine;
    
    const summaryEngine = document.getElementById('summary-engine');
    if (summaryEngine) {
        summaryEngine.textContent = engine + 'cc';
    }
    
    // Update button styles
    document.querySelectorAll('.tiposmotores').forEach(btn => {
        btn.classList.remove('bg-yellow-500', 'border-yellow-500', 'text-black');
        btn.classList.add('bg-gray-800', 'border-gray-700', 'text-white');
    });
    
    const selectedBtn = document.querySelector(`[data-engine="${engine}"]`);
    if (selectedBtn) {
        selectedBtn.classList.remove('bg-gray-800', 'border-gray-700', 'text-white');
        selectedBtn.classList.add('bg-yellow-500', 'border-yellow-500', 'text-black');
    }
    
    updatePrice();
}

function updatePrice() {
    const basePrice = precios[modeloSeleccionado].base;
    const enginePrice = preciosMotores[motorSeleccionado];
    const total = basePrice + enginePrice;
    
    const totalPriceEl = document.getElementById('total-price');
    if (totalPriceEl) {
        totalPriceEl.textContent = '€' + total.toLocaleString();
    }
}

function goToSection(sectionName) {
    const currentIndex = secciones.indexOf(seccionActual);
    const newIndex = secciones.indexOf(sectionName);
    
    if (newIndex === -1 || newIndex === currentIndex) return;
    
    // Hide all sections
    secciones.forEach((section) => {
        const el = document.getElementById(`section-${section}`);
        if (el) {
            el.classList.add('hidden');
        }
    });
    
    // Show target section
    const targetEl = document.getElementById(`section-${sectionName}`);
    if (targetEl) {
        targetEl.classList.remove('hidden');
    }
    
    seccionActual = sectionName;
    updateNavigation();
}

function nextSection() {
    const currentIndex = secciones.indexOf(seccionActual);
    if (currentIndex < secciones.length - 1) {
        goToSection(secciones[currentIndex + 1]);
    }
}

function prevSection() {
    const currentIndex = secciones.indexOf(seccionActual);
    if (currentIndex > 0) {
        goToSection(secciones[currentIndex - 1]);
    }
}

function updateNavigation() {
    const currentIndex = secciones.indexOf(seccionActual);
    
    // Update prev button
    const prevBtn = document.getElementById('prev-btn');
    if (prevBtn) {
        prevBtn.disabled = currentIndex === 0;
    }
    
    // Update next button text
    const nextBtn = document.getElementById('next-btn');
    if (nextBtn) {
        if (currentIndex === secciones.length - 1) {
            nextBtn.textContent = 'Finalizar';
        } else {
            nextBtn.textContent = 'Siguiente →';
        }
    }
    
    // Update step indicators
    secciones.forEach((section, index) => {
        const stepEl = document.getElementById(`step-${index + 1}`);
        if (stepEl) {
            if (index === currentIndex) {
                stepEl.classList.remove('bg-gray-600');
                stepEl.classList.add('bg-yellow-500');
            } else if (index < currentIndex) {
                stepEl.classList.remove('bg-gray-600');
                stepEl.classList.add('bg-yellow-500');
            } else {
                stepEl.classList.remove('bg-yellow-500');
                stepEl.classList.add('bg-gray-600');
            }
        }
    });
}

// Initialize with default selections
document.addEventListener('DOMContentLoaded', function() {
    selectModel('sport');
    selectColor('negro');
    selectEngine('250');
    updateNavigation();
    
    // Add scroll event listener
    document.addEventListener('wheel', handleScroll, { passive: true });
    
    // Add touch event listeners for mobile
    document.addEventListener('touchstart', handleTouchStart, { passive: true });
    document.addEventListener('touchend', handleTouchEnd, { passive: true });
});

function handleScroll(e) {
    if (isScrolling) return;
    
    const currentIndex = secciones.indexOf(seccionActual);
    
    if (e.deltaY > 50 && currentIndex < secciones.length - 1) {
        isScrolling = true;
        goToSection(secciones[currentIndex + 1]);
        
        // Reset after delay
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(() => {
            isScrolling = false;
        }, 1000);
    } else if (e.deltaY < -50 && currentIndex > 0) {
        isScrolling = true;
        goToSection(secciones[currentIndex - 1]);
        
        // Reset after delay
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(() => {
            isScrolling = false;
        }, 1000);
    }
}

function handleTouchStart(e) {
    touchStartY = e.changedTouches[0].screenY;
}

function handleTouchEnd(e) {
    touchEndY = e.changedTouches[0].screenY;
    handleSwipe();
}

function handleSwipe() {
    const currentIndex = secciones.indexOf(seccionActual);
    const diff = touchStartY - touchEndY;
    
    if (Math.abs(diff) > 50) {
        if (diff > 0 && currentIndex < secciones.length - 1) {
            // Swipe up - go to next section
            goToSection(secciones[currentIndex + 1]);
        } else if (diff < 0 && currentIndex > 0) {
            // Swipe down - go to previous section
            goToSection(secciones[currentIndex - 1]);
        }
    }
}
