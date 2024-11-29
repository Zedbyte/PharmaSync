const updatePanelFormulation = document.getElementById("updateFormulationPanel");
const updateOverlayFormulation = document.getElementById("overlay");

function updateFormulationPanel(button) {
    const formulationID = button.getAttribute('data-formulation-id');

    // Fetch batch data from the server
    fetch(`/update-formulation/${formulationID}`)
        .then(response => response.text())
        .then(data => {
            // Load the fetched data into the panel
            
            updatePanelFormulation.innerHTML = data;

            // Show the panel with fade-in and scale-up animation
            if (updatePanelFormulation.classList.contains("hidden")) {
                updatePanelFormulation.classList.remove("hidden");
                updatePanelFormulation.classList.add("flex");

                // Trigger animation
                setTimeout(() => {
                    updatePanelFormulation.classList.add("opacity-100", "scale-100");
                    updatePanelFormulation.classList.remove("opacity-0", "scale-95");
                }, 10);
            }

            // Show the overlay
            updateOverlayFormulation.classList.remove("hidden");
            
            // toggleRemoveButtonsUpdateMedicine();

            const updateFormulationForm = updatePanelFormulation.querySelector(".update__form_formulation_existing");
            
            updateFormulationForm.addEventListener("submit", handleUpdateSubmissionFormulation);
        })
        .catch(error => console.error('Error:', error));
}

function handleUpdateSubmissionFormulation(event) {
    event.preventDefault();

    const formDataFormulation = new FormData(event.target);
    const formulationID = formDataFormulation.get('formulation_id');
    
    fetch(`/update-formulation/${formulationID}`, {
        method: "POST",
        body: formDataFormulation
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
            updateClosePanelFormulation();
            return; // Stop further processing if errors exist
        }

        // On success, update the UI and close the panel
        updateClosePanelFormulation();
        window.location.href = '/medicine-list';
    })
    .catch(error => console.error("Error:", error));
}

function updateClosePanelFormulation() {
    const updatePanelFormulation = document.getElementById('updateFormulationPanel');
    const updateOverlayFormulation = document.getElementById('overlay');

    // Trigger fade-out and scale-down animation
    updatePanelFormulation.classList.add("opacity-0", "scale-95");
    updatePanelFormulation.classList.remove("opacity-100", "scale-100");

    // Hide the panel after the animation completes
    setTimeout(() => {
        updatePanelFormulation.classList.add("hidden");
        updatePanelFormulation.classList.remove("flex");
    }, 300); // Matches the transition duration

    // Hide the overlay
    updateOverlayFormulation.classList.add("hidden");
}


// function toggleRemoveButtonsUpdateMedicine() {
//     const items = document.querySelectorAll('.item__template_update');
//     items.forEach((item, index) => {
//         const removeButton = item.querySelector('.remove-button-update');
//         removeButton.style.display = items.length > 1 ? 'flex' : 'none';
//     });
// }