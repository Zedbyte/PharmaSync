function openLotDeleteModal(button) {
    // Extract data attributes
    const deleteLotID = button.getAttribute('data-lot-id');
    const deleteMaterialID = button.getAttribute('data-material-id');
    console.log(deleteLotID);
    
    // Get modal element
    const modal = document.getElementById('deleteLotModal');

    // Show the modal
    modal.classList.remove('hidden');

    // Set the delete confirmation button's action
    const confirmButton = document.getElementById('confirmLotDeleteButton');
    
    confirmButton.onclick = function () { 
        fetch(`/delete-lot/${deleteLotID}/material/${deleteMaterialID}`, { 
            method: 'POST',
        })        
        .then(response => {
            if (response.ok) {
                window.location.href = '/inventory/raw';
            } else {
                console.error("Failed to delete lot. Status:", response.status);
            }
        })
        .catch(error => console.error('Error:', error));
    };

    // Display the batch details in the modal
    document.getElementById('modalLotId').innerText = `${deleteLotID} of Material ID-${deleteMaterialID}`;
}

function closeLotDeleteModal() {
    document.getElementById('deleteLotModal').classList.add('hidden');
}
