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
        .then(response => response.json())
        .then(data => {            
            if (data.success) {
                // Redirect to the provided path on success
                window.location.href = data.redirect;
            } else {
                // Redirect to the provided path on failure
                console.error('Errors occurred:', data);
                window.location.href = data.redirect;
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
