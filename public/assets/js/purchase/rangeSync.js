const minPriceRange = document.getElementById('min-price');
const maxPriceRange = document.getElementById('max-price');
const minPriceInput = document.getElementById('min-price-input');
const maxPriceInput = document.getElementById('max-price-input');

// Update range input when number input changes
minPriceInput.addEventListener('input', () => {
    const minValue = parseInt(minPriceInput.value);
    const maxValue = parseInt(maxPriceInput.value);

    if (minValue >= maxValue) {
        minPriceInput.value = maxValue - 1; // Restrict to less than max value
    }
    minPriceRange.value = minPriceInput.value;
});

maxPriceInput.addEventListener('input', () => {
    const minValue = parseInt(minPriceInput.value);
    const maxValue = parseInt(maxPriceInput.value);

    if (maxValue <= minValue) {
        maxPriceInput.value = minValue + 1; // Restrict to more than min value
    }
    maxPriceRange.value = maxPriceInput.value;
});

// Update number input when range input changes
minPriceRange.addEventListener('input', () => {
    const minValue = parseInt(minPriceRange.value);
    const maxValue = parseInt(maxPriceRange.value);

    if (minValue >= maxValue) {
        minPriceRange.value = maxValue - 1; // Restrict to less than max value
    }
    minPriceInput.value = minPriceRange.value;
});

maxPriceRange.addEventListener('input', () => {
    const minValue = parseInt(minPriceRange.value);
    const maxValue = parseInt(maxPriceRange.value);

    if (maxValue <= minValue) {
        maxPriceRange.value = minValue + 1; // Restrict to more than min value
    }
    maxPriceInput.value = maxPriceRange.value;
});