const togglePassword = document.querySelector('#togglePassword');
const passwordInput = document.querySelector('#password');
const eye = document.querySelector('.fa-eye-slash');


// Check input content and enable/disable the eye icon
passwordInput.addEventListener('input', function () {
    if (passwordInput.value === '') {
        togglePassword.classList.add('disabled');  // Disable the entire span
        togglePassword.classList.add('opacity-50');  // Make it semi-transparent
    } else {
        togglePassword.classList.remove('disabled');  // Enable the span
        togglePassword.classList.remove('opacity-50');  // Make it fully visible
    }
});

togglePassword.addEventListener('click', function (e) {
    // Check if the icon is disabled before toggling the password visibility
    if (!togglePassword.classList.contains('disabled')) {
        // Toggle the type attribute
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        // Toggle between fa-eye and fa-eye-slash
        if (type === 'password') {
            eye.classList.remove('fa-eye');
            eye.classList.add('fa-eye-slash');
        } else {
            eye.classList.remove('fa-eye-slash');
            eye.classList.add('fa-eye');
        }
    }
});

