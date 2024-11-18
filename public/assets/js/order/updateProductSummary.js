document.addEventListener('DOMContentLoaded', () => {
    const itemsContainer = document.querySelector('#items__container');

    // Utility to fetch batch data for a specific template
    const fetchBatchData = async (medicineId, batchId, template) => {
        const stockLevelSpan = template.querySelector('.stock-level');
        const quantityInput = template.querySelector('.quantity');
        const productSummaryContainer = template.querySelector('#product_summary tbody');
        const totalAmountCell = template.querySelector('tfoot td:last-child');
        const batchNumberError = template.querySelector('#batch-number-error');

        try {
            const response = await fetch(`/order-list/medicines/${medicineId}/batches/${batchId}`);
            const data = await response.json();

            if (data.error) {
                // Hide medicine-related data and show error
                stockLevelSpan.textContent = '';
                quantityInput.max = '';
                productSummaryContainer.innerHTML = '';
                totalAmountCell.textContent = '$0.00';

                batchNumberError.textContent = "Please select a valid batch number.";
                batchNumberError.classList.remove('hidden');
                return;
            }

            // Hide error message if data is valid
            batchNumberError.classList.add('hidden');

            // Update stock level display
            stockLevelSpan.textContent = `In Stock: ${data.stock_level}`;

            // Set maximum quantity limit for input
            quantityInput.max = data.stock_level;

            // Update Product Summary
            const { name, composition, unit_price } = data.medicine_data;
            const { expiry_date } = data;

            const quantity = parseInt(quantityInput.value) || 1; // Default to 1 if no input
            const amount = (unit_price * quantity).toFixed(2);

            const productRow = `
                <tr class="border-b border-gray-200">
                    <td class="max-w-0 py-5 pl-4 pr-3 text-sm sm:pl-0">
                        <div class="font-medium text-gray-900 dark:text-white">${name}</div>
                        <div class="mt-1 truncate text-gray-500 dark:text-gray-100">${composition}</div>
                    </td>
                    <td class="hidden px-3 py-5 text-right text-sm text-gray-500 dark:text-gray-100 sm:table-cell">${expiry_date}</td>
                    <td class="hidden px-3 py-5 text-right text-sm text-gray-500 dark:text-gray-100 sm:table-cell">$${unit_price}</td>
                    <td class="py-5 pl-3 pr-4 text-right text-sm text-gray-500 dark:text-gray-100 sm:pr-0">$${amount}</td>
                </tr>
            `;

            productSummaryContainer.innerHTML = productRow;

            // Update total amount
            updateTotalAmount(amount, totalAmountCell);
        } catch (error) {
            console.error('Error fetching batch data:', error);
            batchNumberError.textContent = 'Failed to fetch batch data.';
            batchNumberError.classList.remove('hidden');
        }
    };

    // Utility to update total amount for a specific template
    const updateTotalAmount = (newAmount, totalAmountCell) => {
        totalAmountCell.textContent = `$${parseFloat(newAmount).toFixed(2)}`;
    };

    // Set up event listeners for a specific template
    const setupTemplateListeners = (template) => {
        const medicineSelect = template.querySelector('.medicine-name');
        const batchSelect = template.querySelector('.batch-number');
        const quantityInput = template.querySelector('.quantity');

        const handleSelectionChange = () => {
            const medicineId = medicineSelect.value;
            const batchId = batchSelect.value;

            if (medicineId && batchId) {
                fetchBatchData(medicineId, batchId, template);
            }
        };

        medicineSelect.addEventListener('change', () => {
            // Reset batch number and summary when medicine changes
            batchSelect.value = '';
            const stockLevelSpan = template.querySelector('.stock-level');
            const productSummaryContainer = template.querySelector('#product_summary tbody');
            const totalAmountCell = template.querySelector('tfoot td:last-child');
            const batchNumberError = template.querySelector('#batch-number-error');

            stockLevelSpan.textContent = '';
            productSummaryContainer.innerHTML = '';
            totalAmountCell.textContent = '$0.00';

            batchNumberError.textContent = 'Please select a batch number for the new medicine.';
            batchNumberError.classList.remove('hidden');
        });

        batchSelect.addEventListener('change', handleSelectionChange);

        quantityInput.addEventListener('input', () => {
            const unitPriceCell = template.querySelector('tr td:nth-child(3)');
            const amountCell = template.querySelector('tr td:nth-child(4)');
            const totalAmountCell = template.querySelector('tfoot td:last-child');

            if (unitPriceCell && amountCell) {
                const unitPrice = parseFloat(unitPriceCell.textContent.replace('$', ''));
                const quantity = parseInt(quantityInput.value) || 1; // Default to 1 if input is invalid
                const amount = (unitPrice * quantity).toFixed(2);

                amountCell.textContent = `$${amount}`;
                updateTotalAmount(amount, totalAmountCell);
            }
        });
    };

    // Initialize listeners for existing templates
    const templates = itemsContainer.querySelectorAll('.item__template');
    templates.forEach(setupTemplateListeners);

    // Optional: Setup listener for dynamically added templates
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.addedNodes.length) {
                mutation.addedNodes.forEach((node) => {
                    if (node.classList && node.classList.contains('item__template')) {
                        setupTemplateListeners(node);
                    }
                });
            }
        });
    });

    observer.observe(itemsContainer, { childList: true });
});
