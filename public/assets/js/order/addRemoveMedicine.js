function addNewItem() {
    const container = document.getElementById('items__container');
    const newItem = document.querySelector('.item__template').cloneNode(true);

    // Reset input and select fields
    newItem.querySelectorAll('input, select').forEach(field => field.value = '');

    // Reset stock level, product summary, and other specific elements
    const stockLevelSpan = newItem.querySelector('.stock-level');
    const productSummaryContainer = newItem.querySelector('#product_summary tbody');
    const totalAmountCell = newItem.querySelector('tfoot td:last-child');
    const batchNumberError = newItem.querySelector('#batch-number-error');

    if (stockLevelSpan) stockLevelSpan.textContent = '';
    if (productSummaryContainer) productSummaryContainer.innerHTML = '';
    if (totalAmountCell) totalAmountCell.textContent = '$0.00';
    if (batchNumberError) {
        batchNumberError.textContent = '';
        batchNumberError.classList.add('hidden');
    }

    container.appendChild(newItem);
    toggleRemoveButtons(); // Check visibility of remove buttons
    initializeFlatpickr(); // Reinitialize Flatpickr if needed
}

function toggleRemoveButtons() {
    const items = document.querySelectorAll('.item__template');

    items.forEach((item, index) => {
        const removeButton = item.querySelector('.remove-button');
        // Show the button only if there is more than one item
        removeButton.style.display = items.length > 1 ? 'flex' : 'none';
    });
}

function removeItem(event) {
    const item = event.target.closest('.item__template');
    if (item) {
        item.remove();
        toggleRemoveButtons(); // Adjust button visibility after removal
    }
}
