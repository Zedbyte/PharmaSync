document.addEventListener("DOMContentLoaded", () => {
    // Function to fetch and populate the medicine names
    async function fetchMedicineNames(medicineNameSelect, type = "%") {
        // Clear existing options in the "Medicine Name" dropdown
        medicineNameSelect.innerHTML = '<option value="" selected disabled>Select Medicine Name</option>';

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

    // Event delegation to handle dynamic elements
    document.body.addEventListener("change", event => {
        const target = event.target;

        if (target.matches(".medicine-type")) {
            const itemTemplate = target.closest(".item__template");
            const medicineNameSelect = itemTemplate.querySelector(".medicine-name");
            fetchMedicineNames(medicineNameSelect, target.value);
        } 
    });
});
