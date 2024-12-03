function openDeleteRackModal(button) {
    // Extract data attributes
    const deleteRackID = button.getAttribute('data-rack-id');
    console.log(deleteRackID);
    
    // Get modal element
    const modal = document.getElementById('deleteRackModal');

    // Show the modal
    modal.classList.remove('hidden');

    // Set the delete confirmation button's action
    const confirmButton = document.getElementById('confirmRackDeleteButton');
    
    confirmButton.onclick = function () { 
        fetch(`/delete-rack/${deleteRackID}`, { 
            method: 'POST',
        })        
        .then(response => {
            if (response.ok) {
                window.location.href = '/batch-list';
            } else {
                console.error("Failed to delete rack. Status:", response.status);
            }
        })
        .catch(error => console.error('Error:', error));
    };

    // Display the rack details in the modal
    document.getElementById('modalRackID').innerText = `${deleteRackID}`;
}

function closeDeleteRackModal() {
    document.getElementById('deleteRackModal').classList.add('hidden');
}
