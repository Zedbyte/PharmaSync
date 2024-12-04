function openUpdateCustomerPanel(customer) {
    document.getElementById('update-customer-id').value = customer.id;
    document.getElementById('update-customer-name').value = customer.name;
    document.getElementById('update-customer-email').value = customer.email;
    document.getElementById('update-customer-address').value = customer.address;
    document.getElementById('update-customer-contact_no').value = customer.contact_no;

    document.getElementById('updateCustomerModal').classList.remove('hidden');
}

function closeUpdateCustomerModal() {
    document.getElementById('updateCustomerModal').classList.add('hidden');
}
