// Function to open the modal
function openAddSupplierModal() {
    document.getElementById('addSupplierModal').classList.remove('hidden');
    document.getElementById('addSupplierOverlay').classList.remove('hidden');
}

// Function to close the modal
function closeAddSupplierModal() {
    document.getElementById('addSupplierModal').classList.add('hidden');
    document.getElementById('addSupplierOverlay').classList.add('hidden');
}
