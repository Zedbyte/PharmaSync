function addPurchaseTogglePanel() {
    const addPanel = document.getElementById("addPurchasePanel");
    const addOverlay = document.getElementById("overlay");

    // Toggle the panel and overlay visibility
    addPanel.classList.toggle("translate-x-full");
    addOverlay.classList.toggle("hidden");
}