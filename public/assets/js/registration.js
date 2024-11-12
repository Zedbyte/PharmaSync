// Select the elements for both password fields and their toggle icons
const passwordToggles = [
    {
        toggleButton: document.querySelector('#togglePassword'),
        passwordInput: document.querySelector('#password')
    },
    {
        toggleButton: document.querySelector('#togglePassword_confirm'),
        passwordInput: document.querySelector('#password_confirm')
    }
];

// Function to enable or disable the eye icon based on input content
function handleToggleButtonState(passwordInput, toggleButton) {
    if (passwordInput.value === '') {
        toggleButton.classList.add('disabled', 'opacity-50');
    } else {
        toggleButton.classList.remove('disabled', 'opacity-50');
    }
}

// Function to toggle visibility of password
function togglePasswordVisibility(toggleButton, passwordInput) {
    if (!toggleButton.classList.contains('disabled')) {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        // Toggle icon appearance
        const icon = toggleButton.querySelector('i');
        if (type === 'password') {
            icon.classList.add('fa-eye-slash');
            icon.classList.remove('fa-eye');
        } else {
            icon.classList.add('fa-eye');
            icon.classList.remove('fa-eye-slash');
        }
    }
}

// Add event listeners for each password input and toggle button
passwordToggles.forEach(({ toggleButton, passwordInput }) => {
    // Enable/disable eye icon based on password input content
    passwordInput.addEventListener('input', () => handleToggleButtonState(passwordInput, toggleButton));

    // Toggle password visibility on icon click
    toggleButton.addEventListener('click', () => togglePasswordVisibility(toggleButton, passwordInput));
});
