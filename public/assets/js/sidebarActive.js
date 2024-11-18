document.addEventListener('DOMContentLoaded', function () {
    const navLinks = document.querySelectorAll('.nav__link_active');
    const collapsibleButtons = document.querySelectorAll('.collapsible');

    // Function to handle active links and expand dropdowns
    function setActiveLink() {
        const currentPath = window.location.pathname;

        navLinks.forEach(link => {
            const linkPath = new URL(link.href).pathname;

            // If the link matches the current path
            if (linkPath === currentPath) {
                link.classList.add('active-light');

                // Expand the parent dropdown if the link is inside a collapsible
                const parentDropdown = link.closest('.nav__collapse');
                if (parentDropdown) {
                    const parentButton = parentDropdown.previousElementSibling;

                    // Ensure dropdown is expanded
                    if (!parentDropdown.classList.contains('expanded')) {
                        toggleCollapse(parentDropdown.id, parentButton); // Use your existing function
                    }
                }
            } else {
                link.classList.remove('active-light');
            }
        });
    }

    // Call the function initially to handle active links and expanded states
    setActiveLink();
});
