document.addEventListener("DOMContentLoaded", () => {
    const medicineTypeSelect = document.getElementById("medicine-type");
    const medicineNameSelect = document.getElementById("medicine-name");

    // Function to fetch and populate the medicine names
    async function fetchMedicineNames(type = "%") {
        // Clear existing options in the "Medicine Name" dropdown
        medicineNameSelect.innerHTML = '<option value="" selected disabled>Select Medicine Name</option>';

        try {
            // Fetch medicine data based on the selected type
            const response = await fetch(`/order-list/t/medicine/${type}`);
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

    // Initial load: fetch all medicine names
    fetchMedicineNames();

    // Update medicine names when the type changes
    medicineTypeSelect.addEventListener("change", () => {
        const selectedType = medicineTypeSelect.value;
        fetchMedicineNames(selectedType);
    });
});
