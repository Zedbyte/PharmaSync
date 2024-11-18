document.addEventListener("DOMContentLoaded", () => {
    // Function to fetch and populate the medicine names
    async function fetchMedicineNames(medicineNameSelect, batchNumberSelect, type = "%") {
        // Clear existing options in the "Medicine Name" dropdown
        medicineNameSelect.innerHTML = '<option value="" selected disabled>Select Medicine Name</option>';
        batchNumberSelect.innerHTML = '<option value="" selected disabled>Select Batch Number</option>'; // Reset batch number dropdown

        try {
            // Fetch medicine data based on the selected type
            const response = await fetch(`/order-list/medicines?type=${type}`);
            if (!response.ok) throw new Error("Network response was not ok");

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

    // Function to fetch and populate the batch numbers
    async function fetchBatchNumbers(batchNumberSelect, medicineId) {
        // Clear existing options in the "Batch Number" dropdown
        batchNumberSelect.innerHTML = '<option value="" selected disabled>Select Batch Number</option>';

        if (!medicineId) return; // Exit if no medicine ID is selected

        try {
            // Fetch batch data based on the selected medicine ID
            const response = await fetch(`/order-list/medicines/${medicineId}/batches`);

            if (!response.ok) throw new Error("Network response was not ok");

            const batches = await response.json();

            // Populate the "Batch Number" dropdown with the fetched data
            batches.forEach(batch => {
                const option = document.createElement("option");
                option.value = batch.batch_id; // Adjust based on your data structure
                option.textContent = "PHS-BATCH-" + batch.batch_id; // Adjust based on your data structure

                batchNumberSelect.appendChild(option);
            });
        } catch (error) {
            console.error("Error fetching batch numbers:", error);
        }
    }

    // Event delegation to handle dynamic elements
    document.body.addEventListener("change", event => {
        const target = event.target;

        if (target.matches(".medicine-type")) {
            const itemTemplate = target.closest(".item__template");
            const medicineNameSelect = itemTemplate.querySelector(".medicine-name");
            const batchNumberSelect = itemTemplate.querySelector(".batch-number");
            fetchMedicineNames(medicineNameSelect, batchNumberSelect, target.value);
        } else if (target.matches(".medicine-name")) {
            const itemTemplate = target.closest(".item__template");
            const batchNumberSelect = itemTemplate.querySelector(".batch-number");
            fetchBatchNumbers(batchNumberSelect, target.value);
        }
    });
});
