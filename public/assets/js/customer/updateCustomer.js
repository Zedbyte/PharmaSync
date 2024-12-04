function openUpdateCustomerModal(customer) {
    console.log('Customer data:', customer); // Debugging

    if (!customer || !customer.id) {
        console.error('Invalid customer data:', customer);
        return;
    }

    const modal = document.getElementById('updateCustomerModal');

    // Populate modal fields
    document.getElementById('updateCustomerId').value = customer.id;
    document.getElementById('updateCustomerName').value = customer.name;
    document.getElementById('updateCustomerEmail').value = customer.email;
    document.getElementById('updateCustomerAddress').value = customer.address;
    document.getElementById('updateCustomerContact').value = customer.contact_no;

    // Show modal with animation
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.add('opacity-100');
        modal.querySelector('.transform').classList.remove('scale-95');
    }, 10);
}


function closeUpdateCustomerModal() {
    const modal = document.getElementById('updateCustomerModal');

    // Hide the modal with animation
    modal.classList.remove('opacity-100');
    modal.querySelector('.transform').classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}
