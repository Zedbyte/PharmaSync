function addNewItemExisting() {
    const container_existing = document.getElementById('items__container_existing');
    const newItem_existing = document.querySelector('.item__template_existing').cloneNode(true);
    newItem_existing.querySelectorAll('input, select').forEach(field => field.value = ''); // Clear values

    container_existing.appendChild(newItem_existing);
    toggleRemoveButtonsExisting();  // Check visibility of remove buttons
    initializeFlatpickr();
}

function toggleRemoveButtonsExisting() {
    const items_existing = document.querySelectorAll('.item__template_existing');
    
    items_existing.forEach((item, index) => {
        const removeButton_existing = item.querySelector('.remove-button');
        // Show the button only if there is more than one item
        removeButton_existing.style.display = items_existing.length > 1 ? 'flex' : 'none';
    });
}

function removeItemExisting(event) {
    const item_existing = event.target.closest('.item__template_existing');
    if (item_existing) {
        item_existing.remove();
        toggleRemoveButtonsExisting();  // Adjust button visibility after removal
    }
}
