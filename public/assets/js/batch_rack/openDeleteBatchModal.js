function openDeleteBatchModal(button) {
    // Extract data attributes
    const deleteBatchID = button.getAttribute('data-batch-id');
    console.log(deleteBatchID);
    
    // Get modal element
    const modal = document.getElementById('deleteBatchModal');

    // Show the modal
    modal.classList.remove('hidden');

    // Set the delete confirmation button's action
    const confirmButton = document.getElementById('confirmBatchDeleteButton');
    
    confirmButton.onclick = function () { 
        fetch(`/delete-batch/${deleteBatchID}`, { 
            method: 'POST',
        })        
        .then(response => {
            if (response.ok) {
                window.location.href = '/batch-list';
            } else {
                console.error("Failed to delete batch. Status:", response.status);
            }
        })
        .catch(error => console.error('Error:', error));
    };

    // Display the batch details in the modal
    document.getElementById('modalBatchID').innerText = `${deleteBatchID}`;
}

function closeDeleteBatchModal() {
    document.getElementById('deleteBatchModal').classList.add('hidden');
}
