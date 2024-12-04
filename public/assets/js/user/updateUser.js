function openUpdateUserModal(user) {
    console.log('User data:', user); // Debugging

    if (!user || !user.id) {
        console.error('Invalid user data:', user);
        return;
    }

    const modal = document.getElementById('updateUserModal');

    // Populate modal fields
    document.getElementById('updateUserId').value = user.id;
    document.getElementById('updateUserFirstName').value = user.first_name;
    document.getElementById('updateUserLastName').value = user.last_name;
    document.getElementById('updateUserEmail').value = user.email_address;
    document.getElementById('updateUserContact').value = user.contact_no;
    document.getElementById('updateUserGender').value = user.gender;
    document.getElementById('updateUserRole').value = user.role;

    // Profile picture
    const profilePicture = document.getElementById('updateUserProfilePicture');
    if (profilePicture) {
        profilePicture.src = `data:image/jpeg;base64,${user.profile_picture}`;
    } else {
        console.error('Profile picture element not found.');
    }

    // Show modal with animation
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.add('opacity-100');
        modal.querySelector('.transform').classList.remove('scale-95');
    }, 10);
}


function closeUpdateUserModal() {
    const modal = document.getElementById('updateUserModal');

    // Hide the modal with animation
    modal.classList.remove('opacity-100');
    modal.querySelector('.transform').classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}
