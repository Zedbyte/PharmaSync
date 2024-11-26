document.addEventListener("DOMContentLoaded", () => {
    // Function to fetch and populate the medicine names
    async function fetchMedicineNames(medicineNameSelect, batchID = null, type = "%") {
        // Clear existing options in the "Medicine Name" dropdown
        medicineNameSelect.innerHTML = '<option value="" selected disabled>Select Medicine Name</option>';

        try {
            // Build the API URL based on whether batchID is provided
            const url = batchID
                ? `/add-existing-batch/batch/${batchID}/medicines?type=${type}`
                : `/add-existing-batch/medicines?type=${type}`;

            const response = await fetch(url);
            if (!response.ok) throw new Error("Failed to fetch medicines");

            const medicines = await response.json();

            // Populate the "Medicine Name" dropdown with the fetched data
            medicines.forEach(medicine => {
                const option = document.createElement("option");
                option.value = medicine.id; // Adjust based on your data structure
                option.textContent = medicine.name; // Adjust based on your data structure
                medicineNameSelect.appendChild(option);
            });
        } catch (error) {
            console.error("Error fetching medicines:", error);
        }
    }

    // Event delegation for dropdown interactions
    document.body.addEventListener("change", event => {
        const target = event.target;
        
        // Case 1: .batch-number dropdown
        if (target.matches(".batch-number")) {
            const batchID = target.value;

            // Find all .item__template_existing instances and update their dropdowns
            document.querySelectorAll(".item__template_existing").forEach(itemTemplate => {
                itemTemplate.querySelectorAll(".medicine-type").forEach(medicineTypeDropdown => {
                    const type = medicineTypeDropdown.value || "%";
                    const medicineNameSelect = itemTemplate.querySelector(".medicine-name");

                    // Fetch medicines for the given batchID and type
                    fetchMedicineNames(medicineNameSelect, batchID, type);
                });
            });
            return; // Stop further processing for this event
        }

        // Case 2: .medicine-type dropdown
        if (target.matches(".medicine-type")) {
            const itemTemplate = target.closest(".item__template_existing") || target.closest(".item__template_new");
            if (itemTemplate) {
                const medicineNameSelect = itemTemplate.querySelector(".medicine-name");
                const type = target.value;

                // Check for .batch-number if inside .item__template_existing
                if (itemTemplate.classList.contains("item__template_existing")) {
                    const batchDropdown = document.querySelector(".batch-number"); // Batch number is outside
                    const batchID = batchDropdown ? batchDropdown.value : null;

                    // Fetch medicines with batchID and type
                    fetchMedicineNames(medicineNameSelect, batchID, type);
                } else {
                    // No batch-number, fetch medicines with only type
                    fetchMedicineNames(medicineNameSelect, null, type);
                }
            }
        }
    });
});
