const panel = document.getElementById("viewPanel");
const overlay = document.getElementById("overlay");

function viewPurchasePanel(button) {
    const purchaseID = button.getAttribute('data-id');

    // Fetch purchase data from the server
    fetch(`/view-purchase/${purchaseID}`)
        .then(response => response.text())
        .then(data => {
            // Load the fetched data into the panel
            panel.innerHTML = data;

            // Toggle panel and overlay visibility
            panel.classList.remove("translate-x-full");
            overlay.classList.remove("hidden");

        })
        .catch(error => console.error('Error:', error));
}

function viewClosePanel() {
    // Toggle panel and overlay visibility
    panel.classList.add("translate-x-full");
    overlay.classList.add("hidden");
    console.log("AND I");
    
}