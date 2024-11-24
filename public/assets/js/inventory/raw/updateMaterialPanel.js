const updatePanel = document.getElementById("updateMaterialPanel");
const updateOverlay = document.getElementById("overlay");

function updateMaterialPanel(button) {
    const materialID = button.getAttribute('data-material-id');

    // Fetch batch data from the server
    fetch(`/update-material/${materialID}`)
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
        
            const updateForm = updatePanel.querySelector(".update__form_material");
            
            updateForm.addEventListener("submit", handleUpdateSubmissionMaterial);
        })
        .catch(error => console.error('Error:', error));
}

function handleUpdateSubmissionMaterial(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    const materialID = formData.get('material_id');

    fetch(`/update-material/${materialID}`, {
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
            updateMaterialClosePanel();
            return; // Stop further processing if errors exist
        }

        // On success, update the UI and close the panel
        updateMaterialClosePanel();
        window.location.href = '/inventory/raw';
    })
    .catch(error => console.error("Error:", error));
}

function updateMaterialClosePanel() {
    const updatePanel = document.getElementById('updateMaterialPanel');
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