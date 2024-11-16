// Select all `li` elements in the tab navigation
const tabItems = document.querySelectorAll('.tabbed__nav li');

// Function to handle the tab switching
function handleTabClick(event) {
    // Get the current `li` that was clicked
    const clickedTab = event.currentTarget;

    // Remove 'tab-item' and restore 'link__underline_hover' from all tabs
    tabItems.forEach((tab) => {
        tab.classList.remove('tab-item');
        tab.classList.add('link__underline_hover');
    });

    // Add 'tab-item' to the clicked tab
    clickedTab.classList.add('tab-item');

    // Temporarily remove 'link__underline_hover' from the active tab
    clickedTab.classList.remove('link__underline_hover');

    // Check if the HTML has the class 'dark'
    // if (document.documentElement.classList.contains('dark')) {
    //     // Add the Tailwind `tab-item-dark` class to the active tab
    //     clickedTab.classList.add('tab-item-dark');
    // }
}

// Attach click event listeners to all tabs
tabItems.forEach((tab) => {
    tab.addEventListener('click', handleTabClick);
});
