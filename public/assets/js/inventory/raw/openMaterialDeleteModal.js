function openMaterialDeleteModal(button) {
    // Extract data attributes
    const materialId = button.getAttribute('data-material-id');
    const lotsCount = button.getAttribute('data-lots-count');

    // Get modal element
    const modal = document.getElementById('deleteMaterialModal');

    // Show the modal
    modal.classList.remove('hidden');

    // Set the delete confirmation button's action
    const confirmButton = document.getElementById('confirmMaterialDeleteButton');
    
    confirmButton.onclick = function () { 
        fetch(`/delete-material/${materialId}`, { 
            method: 'POST',
        })        
        .then(response => {
            if (response.ok) {
                window.location.href = '/inventory/raw';
            } else {
                console.error("Failed to delete material. Status:", response.status);
            }
        })
        .catch(error => console.error('Error:', error));
    };

    // Display the batch details in the modal
    document.getElementById('modalMaterialId').innerText = `${materialId} used in ${lotsCount} purchases`;
}

function closeMaterialDeleteModal() {
    document.getElementById('deleteMaterialModal').classList.add('hidden');
}
