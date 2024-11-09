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

// Function to apply the saved theme and adjust toggle position on page load
function applySavedTheme() {
    const savedTheme = localStorage.getItem('theme');
    const toggle = button.querySelector('div');
    const svg = toggle.querySelector('svg');

    // Remove any existing translate classes first
    toggle.classList.remove('translate-x-0', 'translate-x-5');

    if (savedTheme === 'dark') {
        htmlRoot.classList.add('dark');
        toggle.classList.add('translate-x-5');
        svg.classList.replace('text-amber-500', 'text-violet-500');
        svg.innerHTML = `<path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>`;
    } else {
        // Default to light theme if no theme is saved
        htmlRoot.classList.add('light');
        toggle.classList.add('translate-x-0');
        svg.classList.replace('text-violet-500', 'text-amber-500');
        svg.innerHTML = `<path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path>`;
    }
}

// Apply the saved theme on page load
document.addEventListener('DOMContentLoaded', applySavedTheme);

// Only attach the event listener for the button if it exists
if (button) {
    button.addEventListener('click', toggleTheme);
    button.addEventListener('click', function() {
        const toggle = this.querySelector('div');
        const svg = toggle.querySelector('svg');

        // Toggle the translation and icon color
        toggle.classList.toggle('translate-x-5');
        toggle.classList.toggle('translate-x-0');

        // Toggle icon color and path for dark/light mode
        if (svg.classList.contains('text-amber-500')) {
            svg.classList.replace('text-amber-500', 'text-violet-500');
            svg.innerHTML = `<path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>`;
        } else {
            svg.classList.replace('text-violet-500', 'text-amber-500');
            svg.innerHTML = `<path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path>`;
        }
    });
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