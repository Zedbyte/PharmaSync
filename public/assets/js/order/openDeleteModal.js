function openDeleteModal(button) {
    const orderId = button.getAttribute('data-id');
    const materialCount = button.getAttribute('data-material-count');

    const modal = document.getElementById('deleteOrderModal');
    
    // Display the modal
    modal.classList.remove('hidden');

    // Set the delete confirmation button's action
    const confirmButton = document.getElementById('confirmDeleteButton');
    confirmButton.onclick = function() {
        fetch(`/delete-order/${orderId}`, { 
            method: 'POST',
        })
        .then(response => {
            if (response.ok) {
                window.location.href = `/order-list`;
            } else {
                console.error("Failed to delete order. Status:", response.status);
            }
        })
        .catch(error => console.error('Error:', error));
    };

    // Display the order details in the modal
    document.getElementById('modalOrderId').innerText = orderId;
    document.getElementById('modalMaterialCount').innerText = materialCount;
}

function closeDeleteModal() {
    document.getElementById('deleteOrderModal').classList.add('hidden');
}