function addNewItem() {
    const container = document.getElementById('items__container');
    const newItem = document.querySelector('.item__template').cloneNode(true);
    newItem.querySelectorAll('input, select, textarea').forEach(field => field.value = ''); // Clear values

    container.appendChild(newItem);
    toggleRemoveButtons();  // Check visibility of remove buttons
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
        toggleRemoveButtons();  // Adjust button visibility after removal
    }
}
