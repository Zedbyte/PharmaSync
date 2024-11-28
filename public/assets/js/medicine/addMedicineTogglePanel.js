function addMedicineTogglePanel(panel) {
    const addPanel = document.getElementById(panel);
    const addOverlay = document.getElementById("overlay");

    if (addPanel.classList.contains("hidden")) {
        // Remove 'hidden' and add 'flex' to display the panel
        addPanel.classList.remove("hidden");
        addPanel.classList.add("flex");

        // Trigger fade-in and scale-up animation
        setTimeout(() => {
            addPanel.classList.add("opacity-100", "scale-100");
            addPanel.classList.remove("opacity-0", "scale-95");
        }, 10); // Small timeout to trigger the transition
    } else {
        // Reverse animation and hide the panel
        addPanel.classList.add("opacity-0", "scale-95");
        addPanel.classList.remove("opacity-100", "scale-100");

        // Remove 'flex' and add 'hidden' after animation completes
        setTimeout(() => {
            addPanel.classList.add("hidden");
            addPanel.classList.remove("flex");
        }, 300); // Matches the transition duration
    }

    // Toggle overlay visibility
    addOverlay.classList.toggle("hidden");
}
