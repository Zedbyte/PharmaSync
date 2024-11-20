// Select all `li` elements in the tab navigation
const tabItems = document.querySelectorAll('.tabbed__nav li');

// Function to handle tab switching
function handleTabClick(event) {
    const clickedTab = event.currentTarget;

    // Get the clicked tab's data-value
    const selectedValue = clickedTab.dataset.value;

    // Update tab styles
    updateTabStyles(selectedValue);
}

// Function to update tab styles based on the active tab
function updateTabStyles(activeValue) {
    tabItems.forEach((tab) => {
        const tabValue = tab.dataset.value;

        // Remove or add styles based on the active tab
        if (tabValue === activeValue) {
            tab.classList.add('tab-item');
            tab.classList.remove('link__underline_hover');
        } else {
            tab.classList.remove('tab-item');
            tab.classList.add('link__underline_hover');
        }
    });
}

// Attach click event listeners to all tabs
tabItems.forEach((tab) => {
    tab.addEventListener('click', handleTabClick);
});

// On page load, apply default styles
document.addEventListener('DOMContentLoaded', () => {
    // Set all tabs to default style
    tabItems.forEach((tab) => {
        tab.classList.remove('tab-item');
        tab.classList.add('link__underline_hover');
    });
});
