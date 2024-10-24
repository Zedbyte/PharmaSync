const button = document.querySelector('.theme__btn');
const htmlRoot = document.documentElement;

// Function to toggle theme
function toggleTheme() {

    // Disable transitions for theme change
    htmlRoot.classList.add('disable-transitions');

    if (htmlRoot.classList.contains('light')) {
        htmlRoot.classList.remove('light');
        htmlRoot.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        htmlRoot.classList.remove('dark');
        htmlRoot.classList.add('light');
        localStorage.setItem('theme', 'light');
    }

    // Re-enable transitions after the next frame
    setTimeout(() => {
        htmlRoot.classList.remove('disable-transitions');
    }, 0);
}

// Load and apply the theme from localStorage when the page loads
// function applySavedTheme() {
//     const savedTheme = localStorage.getItem('theme');
//     if (savedTheme) {
//         htmlRoot.classList.remove('light', 'dark'); // Clear both classes first
//         htmlRoot.classList.add(savedTheme); // Apply the saved theme
//     } else {
    // Default to light theme if no theme is saved
//         htmlRoot.classList.add('light');
//     }
// }

// Apply the saved theme on page load for every page
// document.addEventListener('DOMContentLoaded', applySavedTheme);

// Only attach the event listener for the button if it exists
if (button) {
    button.addEventListener('click', toggleTheme);
}
