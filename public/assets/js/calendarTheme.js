    // Conditionally load the dark theme stylesheet if the .dark class is present
    function loadFlatpickrTheme() {
        const isDarkMode = document.documentElement.classList.contains('dark');
        const themeStylesheet = document.createElement('link');
        themeStylesheet.rel = 'stylesheet';
        
        if (isDarkMode) {
            themeStylesheet.href = 'https://npmcdn.com/flatpickr/dist/themes/dark.css';
        } else {
            themeStylesheet.href = 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css';
        }

        document.head.appendChild(themeStylesheet);
    }

    // Initialize flatpickr on the inputs by their IDs
    function initializeFlatpickr() {
        flatpickr("#start-date", {
            dateFormat: "Y-m-d - H:i",
            enableTime: true,
            defaultDate: document.getElementById("start-date").value || null
        });

        flatpickr("#end-date", {
            dateFormat: "Y-m-d - H:i",
            enableTime: true,
            defaultDate: document.getElementById("end-date").value || null
        });

        flatpickr("#purchase-date", {
            dateFormat: "Y-m-d - H:i",
            enableTime: true,
        });

        flatpickr("#expiry-date", {
            dateFormat: "Y-m-d - H:i",
            enableTime: true,
        });

        flatpickr("#inspection-date", {
            dateFormat: "Y-m-d - H:i",
            enableTime: true,
        });
    }

    // Run the functions when the page is loaded
    document.addEventListener('DOMContentLoaded', () => {
        loadFlatpickrTheme();
        initializeFlatpickr();
    });