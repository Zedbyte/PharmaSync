function addNewItem() {
    const container = document.getElementById('items__container');
    const newItem = document.querySelector('.item__template').cloneNode(true);
    newItem.querySelectorAll('input, select, textarea').forEach(field => field.value = ''); // Clear values
    container.appendChild(newItem);
}