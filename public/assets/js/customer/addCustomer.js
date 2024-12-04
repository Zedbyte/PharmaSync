function openAddCustomerModal() {
    const modal = document.getElementById('addCustomerModal');
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.add('opacity-100');
        modal.querySelector('.transform').classList.remove('scale-95');
    }, 10); // Small delay to trigger the animation
}

function closeAddCustomerModal() {
    const modal = document.getElementById('addCustomerModal');
    modal.classList.remove('opacity-100');
    modal.querySelector('.transform').classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300); // Match the animation duration
}
