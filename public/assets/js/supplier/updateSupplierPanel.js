function openUpdateSupplierPanel(supplier) {
    document.getElementById('update-supplier-id').value = supplier.id;
    document.getElementById('update-supplier-name').value = supplier.name;
    document.getElementById('update-supplier-email').value = supplier.email;
    document.getElementById('update-supplier-address').value = supplier.address;
    document.getElementById('update-supplier-contact_no').value = supplier.contact_no;

    document.getElementById('updateSupplierModal').classList.remove('hidden');
}

function closeUpdateSupplierModal() {
    document.getElementById('updateSupplierModal').classList.add('hidden');
}
