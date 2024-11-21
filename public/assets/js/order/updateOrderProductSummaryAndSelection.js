document.addEventListener("DOMContentLoaded", () => {
    // const itemsContainer = document.querySelector("#items__container_update");
    const updatePanel = document.querySelector("#updateOrderPanel");



    /**
     * Fetch medicine names based on the type
     */
    const fetchMedicineNames = async (medicineNameSelect, batchNumberSelect, type = "%") => {
        
        // medicineNameSelect.innerHTML = '<option value="" selected disabled>Select Medicine Name</option>';
        // batchNumberSelect.innerHTML = '<option value="" selected disabled>Select Batch Number</option>';

        try {
            const response = await fetch(`/order-list/medicines?type=${type}`);
            if (!response.ok) throw new Error("Failed to fetch medicines");

            const medicines = await response.json();
            medicines.forEach(medicine => {
                const option = document.createElement("option");
                option.value = medicine.id;
                option.textContent = medicine.name;
                medicineNameSelect.appendChild(option);
            });
        } catch (error) {
            console.error("Error fetching medicine names:", error);
        }
    };

    /**
     * Fetch batch numbers for the selected medicine
     */
    const fetchBatchNumbers = async (batchNumberSelect, medicineId) => {
        // batchNumberSelect.innerHTML = '<option value="" selected disabled>Select Batch Number</option>';

        try {
            const response = await fetch(`/order-list/medicines/${medicineId}/batches`);
            
            if (!response.ok) throw new Error("Failed to fetch batches");

            const batches = await response.json();
            batches.forEach(batch => {
                const option = document.createElement("option");
                option.value = batch.batch_id;
                option.textContent = `PHS-BATCH-${batch.batch_id}`;
                batchNumberSelect.appendChild(option);
            });
        } catch (error) {
            console.error("Error fetching batch numbers:", error);
        }
    };

    /**
     * Fetch batch data for the selected batch
     */
    const fetchBatchData = async (medicineId, batchId, template) => {
        const stockLevelSpan = template.querySelector(".stock-level-update");
        const quantityInput = template.querySelector(".quantity-update");
        const productSummaryContainer = template.querySelector("#product_summary-update tbody");
        const totalAmountCell = template.querySelector("tfoot td:last-child");
        const batchNumberError = template.querySelector("#batch-number-error-update");

        try {
            const response = await fetch(`/order-list/medicines/${medicineId}/batches/${batchId}`);
            const data = await response.json();

            if (data.error) {
                stockLevelSpan.textContent = "";
                quantityInput.max = "";
                productSummaryContainer.innerHTML = "";
                totalAmountCell.textContent = "$0.00";
                batchNumberError.textContent = "Invalid batch number selected.";
                batchNumberError.classList.remove("hidden");
                return;
            }

            batchNumberError.classList.add("hidden");
            stockLevelSpan.textContent = `In Stock: ${data.stock_level}`;
            quantityInput.max = data.stock_level;

            // Update Product Summary
            const { name, composition, unit_price } = data.medicine_data;
            const { expiry_date } = data;
            const quantity = parseInt(quantityInput.value) || 1;
            const amount = (unit_price * quantity).toFixed(2);

            const productRow = `
                <tr class="border-b border-gray-200">
                    <td class="py-5 pl-4 pr-3 text-sm">
                        <div class="font-medium text-gray-900">${name}</div>
                        <div class="mt-1 truncate text-gray-500">${composition}</div>
                    </td>
                    <td class="px-3 py-5 text-right text-sm text-gray-500">${expiry_date}</td>
                    <td class="px-3 py-5 text-right text-sm text-gray-500">$${unit_price}</td>
                    <td class="py-5 pl-3 pr-4 text-right text-sm text-gray-500">$${amount}</td>
                </tr>
            `;
            productSummaryContainer.innerHTML = productRow;

            updateTotalAmount(amount, totalAmountCell);
        } catch (error) {
            console.error("Error fetching batch data:", error);
        }
    };

    /**
     * Update total amount
     */
    const updateTotalAmount = (newAmount, totalAmountCell) => {
        totalAmountCell.textContent = `$${parseFloat(newAmount).toFixed(2)}`;
    };

    /**
     * Set up event listeners for each item template
     */
    const initializeScripts = () => {
        const itemsContainer = updatePanel.querySelector("#items__container_update");

        if (!itemsContainer) {
            console.error("Items container not found.");
            return;
        }

        const setupTemplateListeners = template => {
            const medicineTypeSelect = template.querySelector(".medicine-type-update");
            const medicineNameSelect = template.querySelector(".medicine-name-update");
            const batchNumberSelect = template.querySelector(".batch-number-update");
            const quantityInput = template.querySelector(".quantity-update");
            const batchNumberError = template.querySelector("#batch-number-error-update");

            if (!medicineTypeSelect || !medicineNameSelect || !batchNumberSelect || !quantityInput) {
                console.error("Template elements missing.");
                return;
            }

            fetchMedicineNames(medicineNameSelect, batchNumberSelect, medicineTypeSelect.value || "%");
            if (medicineNameSelect.value) {
                fetchBatchNumbers(batchNumberSelect, medicineNameSelect.value);
                if (batchNumberSelect.value) {
                    fetchBatchData(medicineNameSelect.value, batchNumberSelect.value, template);
                }
            }

            // Handle medicine type change
            medicineTypeSelect.addEventListener("change", () => {
                medicineNameSelect.innerHTML = '<option value="" selected disabled>Select Batch Number</option>';
                fetchMedicineNames(medicineNameSelect, batchNumberSelect, medicineTypeSelect.value);
            });

            // Handle medicine name change
            medicineNameSelect.addEventListener("change", () => {
                batchNumberSelect.innerHTML = '<option value="" selected disabled>Select Batch Number</option>';
                const stockLevelSpan = template.querySelector(".stock-level-update");

                batchNumberError.textContent = 'Please select a batch number for the updated medicine.';
                batchNumberError.classList.remove('hidden');

                if (stockLevelSpan) stockLevelSpan.textContent = "";
                fetchBatchNumbers(batchNumberSelect, medicineNameSelect.value);
            });

            // Handle batch number change
            batchNumberSelect.addEventListener("change", () => {
                const medicineId = medicineNameSelect.value;
                const batchId = batchNumberSelect.value;
                if (medicineId && batchId) fetchBatchData(medicineId, batchId, template);
            });

            // Handle quantity input change
            quantityInput.addEventListener("input", () => {
                const unitPriceCell = template.querySelector("tr td:nth-child(3)");
                const amountCell = template.querySelector("tr td:nth-child(4)");
                const totalAmountCell = template.querySelector("tfoot td:last-child");

                if (unitPriceCell && amountCell && totalAmountCell) {
                    const unitPrice = parseFloat(unitPriceCell.textContent.replace("$", ""));
                    const quantity = parseInt(quantityInput.value) || 1;
                    const amount = (unitPrice * quantity).toFixed(2);

                    amountCell.textContent = `$${amount}`;
                    updateTotalAmount(amount, totalAmountCell);
                }
            });
        };

        const templates = itemsContainer.querySelectorAll(".item__template_update");
        templates.forEach(setupTemplateListeners);

        const observer = new MutationObserver(mutations => {
            mutations.forEach(mutation => {
                mutation.addedNodes.forEach(node => {
                    if (node.classList && node.classList.contains("item__template_update")) {
                        setupTemplateListeners(node);
                    }
                });
            });
        });

        observer.observe(itemsContainer, { childList: true });
    };

    /**
     * Attach event listeners to all buttons
     */
    const showPanelButtons = document.querySelectorAll(".showUpdateOrderPanel");
    showPanelButtons.forEach(button => {
        button.addEventListener("click", event => {
            const orderId = event.currentTarget.dataset.id;

            // Optionally, load data specific to the order
            // console.log("Order ID:", orderId);

            // Display the update panel
            updatePanel.classList.remove("hidden");

            // Initialize the script after the panel is fully shown
            setTimeout(initializeScripts, 300); // Adjust delay to match transition duration
        });
    });
});
