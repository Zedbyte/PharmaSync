function openDeleteModal(button) {
    // Extract data attributes
    const batchId = button.getAttribute('data-batch-id');
    const medicineId = button.getAttribute('data-medicine-id');
    const batchStock = button.getAttribute('data-batch-stock');
    const medicineName = button.getAttribute('data-medicine-name');

    // Get modal element
    const modal = document.getElementById('deleteBatchModal');

    // Show the modal
    modal.classList.remove('hidden');

    // Set the delete confirmation button's action
    const confirmButton = document.getElementById('confirmDeleteButton');
    
    confirmButton.onclick = function () { 
        fetch(`/delete-batch/${medicineId}/${batchId}`, { 
            method: 'POST',
        })        
        .then(response => {
            if (response.ok) {
                window.location.href = '/inventory/manufactured';
            } else {
                console.error("Failed to delete batch. Status:", response.status);
            }
        })
        .catch(error => console.error('Error:', error));
    };

    // Display the batch details in the modal
    document.getElementById('modalBatchId').innerText = `${batchId} with a stock level of ${batchStock}`;
    document.getElementById('modalMedicineId').innerText = `${medicineId} - ${medicineName}`;
}

function closeDeleteModal() {
    document.getElementById('deleteBatchModal').classList.add('hidden');
}
