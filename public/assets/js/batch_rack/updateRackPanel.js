const updatePanelRack = document.getElementById("updateRackPanel");
const updateOverlayRack = document.getElementById("overlay");

function updateRackPanel(button) {
    const rackID = button.getAttribute('data-rack-id');

    // Fetch rack data from the server
    fetch(`/update-rack/${rackID}`)
        .then(response => response.text())
        .then(data => {
            // Load the fetched data into the panel
            
            updatePanelRack.innerHTML = data;

            // Show the panel with fade-in and scale-up animation
            if (updatePanelRack.classList.contains("hidden")) {
                updatePanelRack.classList.remove("hidden");
                updatePanelRack.classList.add("flex");

                // Trigger animation
                setTimeout(() => {
                    updatePanelRack.classList.add("opacity-100", "scale-100");
                    updatePanelRack.classList.remove("opacity-0", "scale-95");
                }, 10);
            }

            // Show the overlay
            updateOverlayRack.classList.remove("hidden");
            
            // toggleRemoveButtonsUpdateMedicine();

            const updateFormulationBatch = updatePanelRack.querySelector(".update__form_rack_existing");
            
            updateFormulationBatch.addEventListener("submit", handleUpdateSubmissionRack);
        })
        .catch(error => console.error('Error:', error));
}

function handleUpdateSubmissionRack(event) {
    event.preventDefault();

    const formDataRack = new FormData(event.target);
    const rackID = formDataRack.get('rack_id');
    
    fetch(`/update-rack/${rackID}`, {
        method: "POST",
        body: formDataRack
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
            updateClosePanelRack();
            return; // Stop further processing if errors exist
        }

        // On success, update the UI and close the panel
        updateClosePanelRack();
        window.location.href = '/batch-list';
    })
    .catch(error => console.error("Error:", error));
}

function updateClosePanelRack() {
    const updatePanelRack = document.getElementById('updateRackPanel');
    const updateOverlayRack = document.getElementById('overlay');

    // Trigger fade-out and scale-down animation
    updatePanelRack.classList.add("opacity-0", "scale-95");
    updatePanelRack.classList.remove("opacity-100", "scale-100");

    // Hide the panel after the animation completes
    setTimeout(() => {
        updatePanelRack.classList.add("hidden");
        updatePanelRack.classList.remove("flex");
    }, 300); // Matches the transition duration

    // Hide the overlay
    updateOverlayRack.classList.add("hidden");
}


// function toggleRemoveButtonsUpdateMedicine() {
//     const items = document.querySelectorAll('.item__template_update');
//     items.forEach((item, index) => {
//         const removeButton = item.querySelector('.remove-button-update');
//         removeButton.style.display = items.length > 1 ? 'flex' : 'none';
//     });
// }