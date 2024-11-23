const updatePanel = document.getElementById("updateBatchPanel");
const updateOverlay = document.getElementById("overlay");

function updateBatchPanel(button) {
    const medicineID = button.getAttribute('data-medicine-id');
    const batchID = button.getAttribute('data-batch-id');

    // Fetch batch data from the server
    fetch(`/update-batch/medicine/${medicineID}/batch/${batchID}`)
        .then(response => response.text())
        .then(data => {
            // Load the fetched data into the panel
    
            updatePanel.innerHTML = data;

            // Show the panel with fade-in and scale-up animation
            if (updatePanel.classList.contains("hidden")) {
                updatePanel.classList.remove("hidden");
                updatePanel.classList.add("flex");

                // Trigger animation
                setTimeout(() => {
                    updatePanel.classList.add("opacity-100", "scale-100");
                    updatePanel.classList.remove("opacity-0", "scale-95");
                }, 10);
            }

            // Show the overlay
            updateOverlay.classList.remove("hidden");
            
            toggleRemoveButtonsUpdate();

            const updateForm = updatePanel.querySelector(".update__form_existing");
            
            updateForm.addEventListener("submit", handleUpdateSubmission);
        })
        .catch(error => console.error('Error:', error));
}

function handleUpdateSubmission(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    const medicineID = formData.get('medicine_id');
    const batchID = formData.get('batch_id');

    fetch(`/update-batch/medicine/${medicineID}/batch/${batchID}`, {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(result => {


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
        window.location.href = '/inventory/manufactured';
    })
    .catch(error => console.error("Error:", error));
}

function updateClosePanel() {
    const updatePanel = document.getElementById('updateBatchPanel');
    const updateOverlay = document.getElementById('overlay');

    // Trigger fade-out and scale-down animation
    updatePanel.classList.add("opacity-0", "scale-95");
    updatePanel.classList.remove("opacity-100", "scale-100");

    // Hide the panel after the animation completes
    setTimeout(() => {
        updatePanel.classList.add("hidden");
        updatePanel.classList.remove("flex");
    }, 300); // Matches the transition duration

    // Hide the overlay
    updateOverlay.classList.add("hidden");
}


function toggleRemoveButtonsUpdate() {
    const items = document.querySelectorAll('.item__template_update');
    items.forEach((item, index) => {
        const removeButton = item.querySelector('.remove-button-update');
        removeButton.style.display = items.length > 1 ? 'flex' : 'none';
    });
}