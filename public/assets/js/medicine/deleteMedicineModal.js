function openDeleteMedicineModal(button) {
    // Extract data attributes
    const medicineId = button.getAttribute('data-medicine-id');

    // Get modal element
    const modal = document.getElementById('deleteMedicineModal');

    // Show the modal
    modal.classList.remove('hidden');

    // Set the delete confirmation button's action
    const confirmButton = document.getElementById('confirmDeleteButtonMedicine');
    
    confirmButton.onclick = function () {

        const deleteMedicine = document.getElementById('delete-medicine').checked;

        // Prepare data for the request
        const formData = new URLSearchParams();
        formData.append('deleteMedicine', deleteMedicine);

        fetch(`/delete-medicine/${medicineId}`, { 
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
    document.getElementById('modalMedicineId').innerText = `${medicineId}`;;
}

function closeDeleteMedicineModal() {
    document.getElementById('deleteMedicineModal').classList.add('hidden');
}
