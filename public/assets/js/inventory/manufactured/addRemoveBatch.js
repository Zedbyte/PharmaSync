function addNewItem() {
    const container = document.getElementById('items__container_new');
    const newItem = document.querySelector('.item__template_new').cloneNode(true);
    newItem.querySelectorAll('input, select').forEach(field => field.value = ''); // Clear values

    container.appendChild(newItem);
    toggleRemoveButtons();  // Check visibility of remove buttons
    initializeFlatpickr();
}

function toggleRemoveButtons() {
    const items = document.querySelectorAll('.item__template_new');
    
    items.forEach((item, index) => {
        const removeButton = item.querySelector('.remove-button-new');
        // Show the button only if there is more than one item
        removeButton.style.display = items.length > 1 ? 'flex' : 'none';
    });
}

function removeItem(event) {
    const item = event.target.closest('.item__template_new');
    if (item) {
        item.remove();
        toggleRemoveButtons();  // Adjust button visibility after removal
    }
}
