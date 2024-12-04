function openDeleteUserModal(userId) {
    const modal = document.getElementById('deleteUserModal');
    document.getElementById('deleteUserId').value = userId;
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.add('opacity-100');
        modal.querySelector('.transform').classList.remove('scale-95');
    }, 10);
}

function closeDeleteUserModal() {
    const modal = document.getElementById('deleteUserModal');
    modal.classList.remove('opacity-100');
    modal.querySelector('.transform').classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}
