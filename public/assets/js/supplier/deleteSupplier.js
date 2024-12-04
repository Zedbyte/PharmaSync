function openDeleteSupplierModal(supplierId) {
    const modal = document.getElementById('deleteSupplierModal');
    document.getElementById('deleteSupplierId').value = supplierId;
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.add('opacity-100');
        modal.querySelector('.transform').classList.remove('scale-95');
    }, 10);
}

function closeDeleteSupplierModal() {
    const modal = document.getElementById('deleteSupplierModal');
    modal.classList.remove('opacity-100');
    modal.querySelector('.transform').classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}
