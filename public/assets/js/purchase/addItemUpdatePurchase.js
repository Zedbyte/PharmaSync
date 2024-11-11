function addNewItemUpdate() {
    const container = document.getElementById('items__container_update');
    const newItem = document.querySelector('.item__template_update').cloneNode(true);

    // Remove id attributes to avoid duplicates
    //newItem.querySelectorAll('[id]').forEach(element => element.removeAttribute('id'));

    // Clear values in cloned item
    newItem.querySelectorAll('input, select, textarea').forEach(field => field.value = '');

    // Append the new item to the container
    container.appendChild(newItem);

    // Add event listener to the remove button
    const removeButton = newItem.querySelector('.remove-button-update');
    removeButton.addEventListener('click', removeItemUpdate);
    
    // Reinitialize Flatpickr if necessary
    initializeFlatpickr();

    // Update remove button visibility
    toggleRemoveButtonsUpdate();
}

function toggleRemoveButtonsUpdate() {
    const items = document.querySelectorAll('.item__template_update');
    items.forEach((item, index) => {
        const removeButton = item.querySelector('.remove-button-update');
        removeButton.style.display = items.length > 1 ? 'flex' : 'none';
    });
}

function removeItemUpdate(event) {
    event.preventDefault();  // Prevent default button behavior
    const item = event.target.closest('.item__template_update');
    if (item) {
        item.remove();
        toggleRemoveButtonsUpdate();  // Adjust button visibility after removal
    }
}

// Initial call to set up the remove button visibility on page load
document.addEventListener('DOMContentLoaded', () => {
    toggleRemoveButtonsUpdate();
});
