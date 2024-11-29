function addNewFormulation() {
    const container = document.getElementById('items__container_formulation_new');
    const newItem = document.querySelector('.item__template_formulation_new').cloneNode(true);
    newItem.querySelectorAll('input, select, textarea').forEach(field => field.value = ''); // Clear values

    container.appendChild(newItem);
    toggleRemoveButtonsFormulation();  // Check visibility of remove buttons
    // initializeFlatpickr();
}

function toggleRemoveButtonsFormulation() {
    const items = document.querySelectorAll('.item__template_formulation_new');
    
    items.forEach((item, index) => {
        const removeButton = item.querySelector('.remove-button-new');
        // Show the button only if there is more than one item
        removeButton.style.display = items.length > 1 ? 'flex' : 'none';
    });
}

function removeFormulation(event) {
    const item = event.target.closest('.item__template_formulation_new');
    if (item) {
        item.remove();
        toggleRemoveButtonsFormulation();  // Adjust button visibility after removal
    }
}
