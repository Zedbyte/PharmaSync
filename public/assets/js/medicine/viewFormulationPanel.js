const toggleViewFormulationPanel = (panel, overlay, isVisible) => {
    if (isVisible) {
        // Show the panel with fade-in and scale-up animation
        if (panel.classList.contains("hidden")) {
            panel.classList.remove("hidden");
            panel.classList.add("flex");

            // Trigger animation
            setTimeout(() => {
                panel.classList.add("opacity-100", "scale-100");
                panel.classList.remove("opacity-0", "scale-95");
            }, 10);
        }
        // Show the overlay
        overlay.classList.remove("hidden");
    } else {
        // Trigger fade-out and scale-down animation
        panel.classList.add("opacity-0", "scale-95");
        panel.classList.remove("opacity-100", "scale-100");

        // Hide the panel after the animation completes
        setTimeout(() => {
            panel.classList.add("hidden");
            panel.classList.remove("flex");
        }, 300); // Matches the transition duration

        // Hide the overlay
        overlay.classList.add("hidden");
    }
};

function viewFormulationPanel(button) {
    const viewFormulationPanel = document.getElementById("viewFormulationPanel");
    const viewFormulationOverlay = document.getElementById("overlay");
    const formulationID = button.getAttribute('data-formulation-id');

    // Fetch order data from the server
    fetch(`/view-formulation/${formulationID}`)
        .then(response => response.text())
        .then(data => {
            
            // Load the fetched data into the panel
            viewFormulationPanel.innerHTML = data;


            // Show panel and overlay
            toggleViewFormulationPanel(viewFormulationPanel, viewFormulationOverlay, true);
        })
        .catch(error => console.error('Error:', error));
}

function viewFormulationClosePanel() {
    const viewFormulationPanel = document.getElementById("viewFormulationPanel");
    const viewFormulationOverlay = document.getElementById("overlay");
    
    // Hide panel and overlay
    toggleViewFormulationPanel(viewFormulationPanel, viewFormulationOverlay, false);
}
