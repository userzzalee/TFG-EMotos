let selectedModel = 'sport';
let selectedColor = 'negro';
let selectedEngine = '250';
let currentSection = 'modelo';

const sections = ['modelo', 'color', 'motor', 'resumen'];
const prices = {
    sport: { base: 4999 },
    cruiser: { base: 5999 }
};

const enginePrices = {
    '250': 0,
    '500': 1000,
    '1000': 2500
};

const colorNames = {
    'negro': 'Negro',
    'dorado': 'Dorado',
    'gris': 'Gris',
    'blanco': 'Blanco'
};

function selectModel(model) {
    selectedModel = model;
    
    const motoName = document.getElementById('moto-name');
    const summaryModel = document.getElementById('summary-model');
    
    if (motoName) {
        motoName.textContent = model.charAt(0).toUpperCase() + model.slice(1);
    }
    if (summaryModel) {
        summaryModel.textContent = model.charAt(0).toUpperCase() + model.slice(1);
    }
    
    // Update button styles
    document.querySelectorAll('.model-btn').forEach(btn => {
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
    selectedColor = color;
    
    const motoColorName = document.getElementById('moto-color-name');
    const summaryColor = document.getElementById('summary-color');
    
    if (motoColorName) {
        motoColorName.textContent = colorNames[color];
    }
    if (summaryColor) {
        summaryColor.textContent = colorNames[color];
    }
    
    // Update button styles
    document.querySelectorAll('.color-btn').forEach(btn => {
        btn.classList.remove('border-yellow-500', 'scale-110');
    });
    
    const selectedBtn = document.querySelector(`[data-color="${color}"]`);
    if (selectedBtn) {
        selectedBtn.classList.add('border-yellow-500', 'scale-110');
    }
    
    updatePrice();
}

function selectEngine(engine) {
    selectedEngine = engine;
    
    const summaryEngine = document.getElementById('summary-engine');
    if (summaryEngine) {
        summaryEngine.textContent = engine + 'cc';
    }
    
    // Update button styles
    document.querySelectorAll('.engine-btn').forEach(btn => {
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
    const basePrice = prices[selectedModel].base;
    const enginePrice = enginePrices[selectedEngine];
    const total = basePrice + enginePrice;
    
    const totalPriceEl = document.getElementById('total-price');
    if (totalPriceEl) {
        totalPriceEl.textContent = '€' + total.toLocaleString();
    }
}

function goToSection(sectionName) {
    const currentIndex = sections.indexOf(currentSection);
    const newIndex = sections.indexOf(sectionName);
    
    if (newIndex === -1 || newIndex === currentIndex) return;
    
    // Hide all sections
    sections.forEach((section) => {
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
    
    currentSection = sectionName;
    updateNavigation();
}

function nextSection() {
    const currentIndex = sections.indexOf(currentSection);
    if (currentIndex < sections.length - 1) {
        goToSection(sections[currentIndex + 1]);
    }
}

function prevSection() {
    const currentIndex = sections.indexOf(currentSection);
    if (currentIndex > 0) {
        goToSection(sections[currentIndex - 1]);
    }
}

function updateNavigation() {
    const currentIndex = sections.indexOf(currentSection);
    
    // Update prev button
    const prevBtn = document.getElementById('prev-btn');
    if (prevBtn) {
        prevBtn.disabled = currentIndex === 0;
    }
    
    // Update next button text
    const nextBtn = document.getElementById('next-btn');
    if (nextBtn) {
        if (currentIndex === sections.length - 1) {
            nextBtn.textContent = 'Finalizar';
        } else {
            nextBtn.textContent = 'Siguiente →';
        }
    }
    
    // Update step indicators
    sections.forEach((section, index) => {
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
});
