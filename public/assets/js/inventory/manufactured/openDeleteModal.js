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

        const deleteBatch = document.getElementById('delete-batch').checked;

        // Prepare data for the request
        const formData = new URLSearchParams();
        formData.append('deleteBatch', deleteBatch);

        fetch(`/delete-batch/${medicineId}/${batchId}`, { 
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

    // Display the batch details in the modal
    document.getElementById('modalBatchId').innerText = `${batchId} with a stock level of ${batchStock}`;
    document.getElementById('modalMedicineId').innerText = `${medicineId} - ${medicineName}`;
}

function closeDeleteModal() {
    document.getElementById('deleteBatchModal').classList.add('hidden');
}
