const updateLotPanelDiv = document.getElementById("updateLotPanel");
const updateLotOverlay = document.getElementById("overlay");

function updateLotPanel(button) {
    const lotID = button.getAttribute('data-lot-id');
    const materialID = button.getAttribute('data-material-id');

    // Fetch batch data from the server
    fetch(`/update-lot/${lotID}/material/${materialID}`)
        .then(response => response.text())
        .then(data => {
            // Load the fetched data into the panel
    
            updateLotPanelDiv.innerHTML = data;


            // Show the panel with fade-in and scale-up animation
            if (updateLotPanelDiv.classList.contains("hidden")) {
                updateLotPanelDiv.classList.remove("hidden");
                updateLotPanelDiv.classList.add("flex");

                // Trigger animation
                setTimeout(() => {
                    updateLotPanelDiv.classList.add("opacity-100", "scale-100");
                    updateLotPanelDiv.classList.remove("opacity-0", "scale-95");
                }, 10);
            }

            // Show the overlay
            updateLotOverlay.classList.remove("hidden");
        
            const updateForm = updateLotPanelDiv.querySelector(".update__form_lot");
            
            updateForm.addEventListener("submit", handleUpdateSubmissionLot);
        })
        .catch(error => console.error('Error:', error));
}

function handleUpdateSubmissionLot(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    const lotID = formData.get('lot_id');
    const materialID = formData.get('material_id');

    fetch(`/update-lot/${lotID}/material/${materialID}`, {
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
            updateLotClosePanel();
            return; // Stop further processing if errors exist
        }

        // On success, update the UI and close the panel
        updateLotClosePanel();
        window.location.href = '/inventory/raw';
    })
    .catch(error => console.error("Error:", error));
}

function updateLotClosePanel() {
    const updateLotPanelDiv = document.getElementById('updateLotPanel');
    const updateOverlay = document.getElementById('overlay');

    // Trigger fade-out and scale-down animation
    updateLotPanelDiv.classList.add("opacity-0", "scale-95");
    updateLotPanelDiv.classList.remove("opacity-100", "scale-100");

    // Hide the panel after the animation completes
    setTimeout(() => {
        updateLotPanelDiv.classList.add("hidden");
        updateLotPanelDiv.classList.remove("flex");
    }, 300); // Matches the transition duration

    // Hide the overlay
    updateOverlay.classList.add("hidden");
}