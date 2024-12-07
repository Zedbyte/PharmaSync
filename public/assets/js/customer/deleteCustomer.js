function openDeleteCustomerModal(customerId) {
    const modal = document.getElementById('deleteCustomerModal');
    const deleteCustomerId = document.getElementById('deleteCustomerId');

    // Set the customer ID in the hidden input field
    deleteCustomerId.value = customerId;

    // Show the modal with animation
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.add('opacity-100');
        modal.querySelector('.transform').classList.remove('scale-95');
    }, 10); // Small delay to trigger the animation
}

function closeDeleteCustomerModal() {
    const modal = document.getElementById('deleteCustomerModal');

    // Hide the modal with animation
    modal.classList.remove('opacity-100');
    modal.querySelector('.transform').classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300); // Match the animation duration
}
