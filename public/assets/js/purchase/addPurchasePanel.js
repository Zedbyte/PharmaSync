function addPurchaseTogglePanel(panel) {
    const addPanel = document.getElementById(panel);
    const addOverlay = document.getElementById("overlay");

    // Toggle the panel and overlay visibility
    addPanel.classList.toggle("translate-x-full");
    addOverlay.classList.toggle("hidden");
}