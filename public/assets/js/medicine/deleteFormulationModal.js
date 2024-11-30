function openFormulationDeleteModal(button) {
    // Extract data attributes
    const deleteFormulationID = button.getAttribute('data-formulation-id');
    const deleteMedicineID = button.getAttribute('data-medicine-id');
    console.log(deleteFormulationID);
    
    // Get modal element
    const modal = document.getElementById('deleteFormulationModal');

    // Show the modal
    modal.classList.remove('hidden');

    // Set the delete confirmation button's action
    const confirmButton = document.getElementById('confirmFormulationDeleteButton');
    
    confirmButton.onclick = function () { 
        fetch(`/delete-formulation/${deleteFormulationID}`, { 
            method: 'POST',
        })        
        .then(response => {
            if (response.ok) {
                window.location.href = '/medicine-list';
            } else {
                console.error("Failed to delete formulation. Status:", response.status);
            }
        })
        .catch(error => console.error('Error:', error));
    };

    // Display the batch details in the modal
    document.getElementById('modalFormulationID').innerText = `${deleteFormulationID} from Medicine ID: ${deleteMedicineID}`;
}

function closeDeleteFormulationModal() {
    document.getElementById('deleteFormulationModal').classList.add('hidden');
}
