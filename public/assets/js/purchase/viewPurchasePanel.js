const viewPanel = document.getElementById("viewPurchasePanel");
const viewOverlay = document.getElementById("overlay");

function viewPurchasePanel(button) {
    const purchaseID = button.getAttribute('data-id');

    // Fetch purchase data from the server
    fetch(`/view-purchase/${purchaseID}`)
        .then(response => response.text())
        .then(data => {
            // Load the fetched data into the panel
            viewPanel.innerHTML = data;

            // Toggle panel and overlay visibility
            viewPanel.classList.remove("translate-x-full");
            viewOverlay.classList.remove("hidden");

        })
        .catch(error => console.error('Error:', error));
}

function viewClosePanel() {
    // Toggle panel and overlay visibility
    viewPanel.classList.add("translate-x-full");
    viewOverlay.classList.add("hidden");    
}