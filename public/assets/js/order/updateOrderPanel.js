const updatePanel = document.getElementById("updateOrderPanel");
const updateOverlay = document.getElementById("overlay");

function updateOrderPanel(button) {
    const orderID = button.getAttribute('data-id');

    // Fetch order data from the server
    fetch(`/update-order/${orderID}`)
        .then(response => response.text())
        .then(data => {
            // Load the fetched data into the panel
            updatePanel.innerHTML = data;

            // Toggle panel and overlay visibility
            updatePanel.classList.remove("translate-x-full");
            updateOverlay.classList.remove("hidden");
            
            toggleRemoveButtonsUpdate();

            const updateForm = updatePanel.querySelector(".update__form");
            updateForm.addEventListener("submit", handleUpdateSubmission);
        })
        .catch(error => console.error('Error:', error));
}

function handleUpdateSubmission(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    const orderID = formData.get("order_id");

    fetch(`/update-order/${orderID}`, {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(result => {

        console.log(result); return;
        

        if (!result.success && result.errors) {
            // Find or create the error container
            let errorContainer = document.querySelector('.error-container');
            if (!errorContainer) {
                errorContainer = document.createElement('div');
                errorContainer.className = 'error-container bg-orange-100 border-l-4 border-orange-500 text-orange-700 p-4 fixed top-0 left-0 right-0 z-[999] transition-all duration-300';
                document.body.prepend(errorContainer);
            }

            // Populate the errors
            errorContainer.innerHTML = `
                <p class="font-bold">Warning</p>
                <ul>
                    ${result.errors.map(error => `<li>${error}</li>`).join('')}
                </ul>
            `;
            updateClosePanel();
            return; // Stop further processing if errors exist
        }

        // On success, update the UI and close the panel
        updateClosePanel();
        window.location.href = '/order-list';
    })
    .catch(error => console.error("Error:", error));
}

function updateClosePanel() {
    // Toggle panel and overlay visibility
    updatePanel.classList.add("translate-x-full");
    updateOverlay.classList.add("hidden");    
}

function toggleRemoveButtonsUpdate() {
    const items = document.querySelectorAll('.item__template_update');
    items.forEach((item, index) => {
        const removeButton = item.querySelector('.remove-button-update');
        removeButton.style.display = items.length > 1 ? 'flex' : 'none';
    });
}