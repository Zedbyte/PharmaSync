function openDeleteModal(button) {
    const purchaseId = button.getAttribute('data-id');
    const materialCount = button.getAttribute('data-material-count');

    const modal = document.getElementById('deletePurchaseModal');
    
    // Display the modal
    modal.classList.remove('hidden');

    // Set the delete confirmation button's action
    const confirmButton = document.getElementById('confirmDeleteButton');
    confirmButton.onclick = function() {
        fetch(`/delete-purchase/${purchaseId}`, { 
            method: 'POST',
        })
        .then(response => {
            if (response.ok) {
                window.location.href = `/purchase-list`;
            } else {
                console.error("Failed to delete purchase. Status:", response.status);
            }
        })
        .catch(error => console.error('Error:', error));
    };

    // Display the purchase details in the modal
    document.getElementById('modalPurchaseId').innerText = purchaseId;
    document.getElementById('modalMaterialCount').innerText = materialCount;
}

function closeDeleteModal() {
    document.getElementById('deletePurchaseModal').classList.add('hidden');
}