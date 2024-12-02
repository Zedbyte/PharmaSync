const updatePanelBatch = document.getElementById("updateBatchPanel");
const updateOverlayBatch = document.getElementById("overlay");

function updateBatchPanel(button) {
    const batch = button.getAttribute('data-batch-id');

    // Fetch batch data from the server
    fetch(`/update-batch/${batch}`)
        .then(response => response.text())
        .then(data => {
            // Load the fetched data into the panel
            
            updatePanelBatch.innerHTML = data;

            // Show the panel with fade-in and scale-up animation
            if (updatePanelBatch.classList.contains("hidden")) {
                updatePanelBatch.classList.remove("hidden");
                updatePanelBatch.classList.add("flex");

                // Trigger animation
                setTimeout(() => {
                    updatePanelBatch.classList.add("opacity-100", "scale-100");
                    updatePanelBatch.classList.remove("opacity-0", "scale-95");
                }, 10);
            }

            // Show the overlay
            updateOverlayBatch.classList.remove("hidden");
            
            // toggleRemoveButtonsUpdateMedicine();

            const updateFormulationBatch = updatePanelBatch.querySelector(".update__form_batch_existing");
            
            updateFormulationBatch.addEventListener("submit", handleUpdateSubmissionBatch);
        })
        .catch(error => console.error('Error:', error));
}

function handleUpdateSubmissionBatch(event) {
    event.preventDefault();

    const formDataBatch = new FormData(event.target);
    const batchID = formDataBatch.get('batch_id');
    
    fetch(`/update-batch/${batchID}`, {
        method: "POST",
        body: formDataBatch
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
            updateClosePanelBatch();
            return; // Stop further processing if errors exist
        }

        // On success, update the UI and close the panel
        updateClosePanelBatch();
        window.location.href = '/batch-list';
    })
    .catch(error => console.error("Error:", error));
}

function updateClosePanelBatch() {
    const updatePanelBatch = document.getElementById('updateBatchPanel');
    const updateOverlayBatch = document.getElementById('overlay');

    // Trigger fade-out and scale-down animation
    updatePanelBatch.classList.add("opacity-0", "scale-95");
    updatePanelBatch.classList.remove("opacity-100", "scale-100");

    // Hide the panel after the animation completes
    setTimeout(() => {
        updatePanelBatch.classList.add("hidden");
        updatePanelBatch.classList.remove("flex");
    }, 300); // Matches the transition duration

    // Hide the overlay
    updateOverlayBatch.classList.add("hidden");
}


// function toggleRemoveButtonsUpdateMedicine() {
//     const items = document.querySelectorAll('.item__template_update');
//     items.forEach((item, index) => {
//         const removeButton = item.querySelector('.remove-button-update');
//         removeButton.style.display = items.length > 1 ? 'flex' : 'none';
//     });
// }