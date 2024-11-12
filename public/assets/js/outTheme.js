const button = document.querySelector('.theme__btn'); //Returns the first element that is a descendant of node that matches selectors.
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

// Function to apply the saved theme and adjust toggle position on page load
function applySavedTheme() {
    const savedTheme = localStorage.getItem('theme');

    if (savedTheme === 'dark') {
        htmlRoot.classList.add('dark');
    } else {
        // Default to light theme if no theme is saved
        htmlRoot.classList.add('light');
    }
}

// Apply the saved theme on page load
document.addEventListener('DOMContentLoaded', applySavedTheme);

// Only attach the event listener for the button if it exists
if (button) {
    button.addEventListener('click', toggleTheme);
}