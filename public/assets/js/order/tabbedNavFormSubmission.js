document.addEventListener("DOMContentLoaded", () => {
    const tabbedNavForm = document.querySelector(".tabbed-nav-form");

    if (tabbedNavForm) {
        const listItems = tabbedNavForm.querySelectorAll("ul.tabbed__nav > li");

        // Function to handle tab switching and form submission
        const handleTabClick = (event) => {
            event.preventDefault();

            const clickedTab = event.currentTarget;
            const targetValue = clickedTab.dataset.value || clickedTab.textContent.trim();

            // Update tab styles
            updateTabStyles(targetValue);

            // Prepare form data and submit
            const formData = new FormData(tabbedNavForm);
            formData.append("selectedTab", targetValue);

            fetch("/order-list/filter", {
                method: "POST",
                body: formData,
            })
                .then((response) => {
                    if (response.redirected) {
                        window.location.href = response.url;
                    } else {
                        return response.json();
                    }
                })
                .then((data) => {
                    console.log("Success:", data);
                })
                .catch((error) => {
                    console.error("Error:", error.message);
                });
        };

        // Function to update tab styles
        const updateTabStyles = (activeValue) => {
            listItems.forEach((tab) => {
                if (tab.dataset.value === activeValue) {
                    tab.classList.add("tab-item");
                    tab.classList.remove("link__underline_hover");
                } else {
                    tab.classList.remove("tab-item");
                    tab.classList.add("link__underline_hover");
                }
            });
        };

        // Attach event listeners to tabs
        listItems.forEach((listItem) => {
            listItem.addEventListener("click", handleTabClick);
        });

        // Apply styles based on current query parameter
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get("tab") || "all"; // Default to 'all'

        if (window.location.pathname === "/order-list") {
            updateTabStyles(activeTab);
        } else {
            // Reset all styles if not on `/order-list`
            listItems.forEach((tab) => {
                tab.classList.remove("tab-item");
                tab.classList.add("link__underline_hover");
            });
        }
    }
});
