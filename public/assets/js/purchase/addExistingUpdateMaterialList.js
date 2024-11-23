document.addEventListener("DOMContentLoaded", () => {
    // Function to fetch and populate the material names
    async function fetchMaterialNames(materialNameSelect, type = "%") {
        // Clear existing options in the "Naterial Name" dropdown
        materialNameSelect.innerHTML = '<option value="" selected disabled>Select Material Name</option>';

        try {
            // Fetch material data based on the selected type
            const response = await fetch(`/add-purchase-existing/material?type=${type}`);
            console.log(type);
            
            if (!response.ok) throw new Error("Network response was not ok");

            const materials = await response.json();

            // Populate the "Material Name" dropdown with the fetched data
            materials.forEach(material => {
                const option = document.createElement("option");
                option.value = material.id; // Adjust based on your data structure
                option.textContent = material.name; // Adjust based on your data structure
                materialNameSelect.appendChild(option);
            });
        } catch (error) {
            console.error("Error fetching materials:", error);
        }
    }


    // Event delegation to handle dynamic elements
    document.body.addEventListener("change", event => {
        const target = event.target;

        if (target.matches(".material-type")) {
            const itemTemplate = target.closest(".item__template");
            const materialNameSelect = itemTemplate.querySelector(".material-name");
            fetchMaterialNames(materialNameSelect, target.value);
        } 
    });
});
