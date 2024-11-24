function addNewItemExisting() {
    const container = document.getElementById('items__container_existing');
    const newItem = document.querySelector('.item__template_existing').cloneNode(true);
    newItem.querySelectorAll('input, select, textarea').forEach(field => field.value = ''); // Clear values

    container.appendChild(newItem);
    toggleRemoveButtonsExisting();  // Check visibility of remove buttons
    initializeFlatpickr();
}

function toggleRemoveButtonsExisting() {
    const items = document.querySelectorAll('.item__template_existing');
    
    items.forEach((item, index) => {
        const removeButton = item.querySelector('.remove-button-existing');
        // Show the button only if there is more than one item
        removeButton.style.display = items.length > 1 ? 'flex' : 'none';
    });
}

function removeItemExisting(event) {
    const item = event.target.closest('.item__template_existing');
    if (item) {
        item.remove();
        toggleRemoveButtonsExisting();  // Adjust button visibility after removal
    }
}
