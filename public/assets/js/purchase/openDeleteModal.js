function openDeleteModal(button) {
    const purchaseId = button.getAttribute('data-id');
    const materialCount = button.getAttribute('data-material-count');

    const modal = document.getElementById('deletePurchaseModal');
    
    // Display the modal
    modal.classList.remove('hidden');

    // Set the delete confirmation button's action
    const confirmButton = document.getElementById('confirmDeleteButton');
    confirmButton.onclick = function() {
        // Get the checkbox value
        const deleteMaterials = document.getElementById('delete-materials').checked;

        // Prepare data for the request
        const formData = new URLSearchParams();
        formData.append('deleteMaterials', deleteMaterials);

        fetch(`/delete-purchase/${purchaseId}`, { 
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: formData.toString(),
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
