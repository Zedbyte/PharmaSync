function openAddUserModal() {
    const modal = document.getElementById('addUserModal');
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.add('opacity-100');
        modal.querySelector('.transform').classList.remove('scale-95');
    }, 10);
}

function closeAddUserModal() {
    const modal = document.getElementById('addUserModal');
    modal.classList.remove('opacity-100');
    modal.querySelector('.transform').classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}
