const viewPanel = document.getElementById("viewOrderPanel");
const viewOverlay = document.getElementById("overlay");

function viewOrderPanel(button) {
    const orderID = button.getAttribute('data-id');

    // Fetch order data from the server
    fetch(`/view-order/${orderID}`)
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