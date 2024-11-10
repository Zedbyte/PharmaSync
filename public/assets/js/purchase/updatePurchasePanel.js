const updatePanel = document.getElementById("updatePurchasePanel");
const updateOverlay = document.getElementById("overlay");

function updatePurchasePanel(button) {
    const purchaseID = button.getAttribute('data-id');

    // Fetch purchase data from the server
    fetch(`/update-purchase/${purchaseID}`)
        .then(response => response.text())
        .then(data => {
            // Load the fetched data into the panel
            updatePanel.innerHTML = data;

            // Toggle panel and overlay visibility
            updatePanel.classList.remove("translate-x-full");
            updateOverlay.classList.remove("hidden");

            const updateForm = updatePanel.querySelector(".update__form");
            updateForm.addEventListener("submit", handleUpdateSubmission);
        })
        .catch(error => console.error('Error:', error));
}

function handleUpdateSubmission(event) {
    event.preventDefault();

    const formData = new FormData(event.target); 
    const purchaseID = formData.get("purchase_id");

    // Send the updated data back to the server via AJAX
    fetch(`/update-purchase/${purchaseID}`, {
        method: "POST",
        body: formData
    })
    .then(response => {
        if (response.ok) {
            // Handle successful update, e.g., close the panel and refresh data    
            updateClosePanel();
            window.location.href = `/purchase-list`;
        } else {
            // Handle error, e.g., display an error message
            console.error("Update failed:", result.error);
        }
    })
    .catch(error => console.error("Error:", error));
}

function updateClosePanel() {
    // Toggle panel and overlay visibility
    updatePanel.classList.add("translate-x-full");
    updateOverlay.classList.add("hidden");    
}