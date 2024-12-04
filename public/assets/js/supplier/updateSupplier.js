function openUpdateSupplierModal(supplier) {
    console.log('Supplier data:', supplier); // Debugging

    if (!supplier || !supplier.id) {
        console.error('Invalid supplier data:', supplier);
        return;
    }

    const modal = document.getElementById('updateSupplierModal');

    // Populate modal fields
    document.getElementById('updateSupplierId').value = supplier.id;
    document.getElementById('updateSupplierName').value = supplier.name;
    document.getElementById('updateSupplierEmail').value = supplier.email;
    document.getElementById('updateSupplierAddress').value = supplier.address;
    document.getElementById('updateSupplierContact').value = supplier.contact_no;

    // Show modal with animation
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.add('opacity-100');
        modal.querySelector('.transform').classList.remove('scale-95');
    }, 10);
}

function closeUpdateSupplierModal() {
    const modal = document.getElementById('updateSupplierModal');

    // Hide the modal with animation
    modal.classList.remove('opacity-100');
    modal.querySelector('.transform').classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}
