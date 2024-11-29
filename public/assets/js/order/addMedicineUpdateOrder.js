function addNewItemUpdate() {
    const container = document.getElementById('items__container_update');
    let template = document.querySelector('.item__template_update');

        // Check if the template exists
        if (!template) {
            // Dynamically create the `item__template_update` structure
            template = document.createElement('div');
            template.className = 'item__template_update relative grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 border-b border-gray-300 dark:border-gray-600 py-6';
    
            // Hidden input for medicine ID
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'medicine_id[]';
            template.appendChild(hiddenInput);
    
            // Medicine Type
            const medicineTypeWrapper = document.createElement('div');
            const medicineTypeLabel = document.createElement('label');
            medicineTypeLabel.htmlFor = 'medicine-type-id';
            medicineTypeLabel.className = 'block text-sm font-medium';
            medicineTypeLabel.textContent = 'Medicine Type';
            medicineTypeWrapper.appendChild(medicineTypeLabel);
    
            const medicineTypeSelect = document.createElement('select');
            medicineTypeSelect.id = 'medicine-type-id';
            medicineTypeSelect.name = 'medicine_type[]';
            medicineTypeSelect.className = 'medicine-type-update mt-1 block w-full h-12 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 px-4';
            medicineTypeSelect.innerHTML = `
                <option value="" disabled>Select Medicine Type</option>
                <option value="%">All</option>
                <option value="liquid">Liquid</option>
                <option value="tablet">Tablet</option>
                <option value="capsule">Capsule</option>
                <option value="topical">Topical Medicine</option>
                <option value="suppository">Suppository</option>
                <option value="drops">Drops</option>
                <option value="inhaler">Inhaler</option>
                <option value="injection">Injection</option>
                <option value="implant_patch">Implant or Patch</option>
                <option value="buccal_sublingual">Buccal or Sublingual</option>
            `;
            medicineTypeWrapper.appendChild(medicineTypeSelect);
            template.appendChild(medicineTypeWrapper);
    
            // Repeat for other fields (Medicine Name, Batch Number, Quantity, etc.)
            const medicineNameWrapper = document.createElement('div');
            medicineNameWrapper.innerHTML = `
                <label for="medicine-name-id" class="block text-sm font-medium">Medicine Name</label>
                <select id="medicine-name-id" name="medicine_name[]" class="medicine-name-update mt-1 block w-full h-12 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 px-4">
                    <option value="" disabled>Select Medicine Name</option>
                </select>
            `;
            template.appendChild(medicineNameWrapper);
    
            const batchNumberWrapper = document.createElement('div');
            batchNumberWrapper.innerHTML = `
                <label for="batch-number-id" class="block text-sm font-medium">Batch Number</label>
                <select id="batch-number-id" name="batch_number[]" class="batch-number-update mt-1 block w-full h-12 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 px-4">
                    <option value="" disabled>Select Batch Number</option>
                </select>
                <p id="batch-number-error-update" class="mt-2 text-sm text-red-500 hidden">Please select a valid batch number.</p>
            `;
            template.appendChild(batchNumberWrapper);
    
            // Quantity field
            const quantityWrapper = document.createElement('div');
            quantityWrapper.innerHTML = `
                <label for="quantity-id" class="text-sm font-medium flex justify-between">
                    Quantity 
                    <span><small class="stock-level-update text-gray-500 dark:text-gray-200">In Stock:</small></span>
                </label>
                <input type="number" id="quantity-id" name="quantity[]" placeholder="1" min="1" class="quantity-update mt-1 block w-full h-12 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 px-4">
            `;
            template.appendChild(quantityWrapper);
    
            // Remove button
            const removeButtonWrapper = document.createElement('div');
            removeButtonWrapper.className = 'absolute right-0 top-[-15px] mb-4';
            removeButtonWrapper.innerHTML = `
                <button onclick="removeItemUpdate(event)" class="remove-button-update hidden bg-red-500 hover:bg-red-600 text-white px-2 py-2 rounded transition-all duration-150 items-center space-x-1"> 
                    <svg class="w-2.5 h-2.5" xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M24 20.188l-8.315-8.209 8.2-8.282-3.697-3.697-8.212 8.318-8.31-8.203-3.666 3.666 8.321 8.24-8.206 8.313 3.666 3.666 8.237-8.318 8.285 8.203z"/>
                    </svg>  
                    <p class="text-sm">Remove</p>
                </button>
            `;
            template.appendChild(removeButtonWrapper);


            const productSummaryUpdate = document.createElement("div");
            productSummaryUpdate.className =
                "col-span-2 p-6 bg-slate-200 dark:bg-slate-600 rounded-lg shadow-sm my-6 overflow-x-auto";
            productSummaryUpdate.id = "product_summary-update";
            productSummaryUpdate.innerHTML = `
                <div class="grid grid-cols-2 items-center">
                    <div>
                        <h1 class="font-bold">Product Summary</h1>
                    </div>
                </div>
                <div class="product-summary -mx-4 mt-8 flow-root sm:mx-0">
                    <table class="min-w-full">
                        <colgroup>
                            <col class="w-full sm:w-1/2">
                            <col class="sm:w-1/6">
                            <col class="sm:w-1/6">
                            <col class="sm:w-1/6">
                        </colgroup>
                        <thead class="border-b border-gray-300 text-gray-900 dark:text-gray-100">
                            <tr>
                                <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold sm:pl-0">Items</th>
                                <th class="hidden px-3 py-3.5 text-right text-sm font-semibold sm:table-cell">Expiration Date</th>
                                <th class="hidden px-3 py-3.5 text-right text-sm font-semibold sm:table-cell">Unit Price</th>
                                <th class="py-3.5 pl-3 pr-4 text-right text-sm font-semibold sm:pr-0">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-200">
                                <td class="max-w-0 py-5 pl-4 pr-3 text-sm sm:pl-0">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ medicine.medicine_id }}</div>
                                    <div class="mt-1 truncate text-gray-500 dark:text-gray-100">{{ medicine.composition }}</div>
                                </td>
                                <td class="hidden px-3 py-5 text-right text-sm text-gray-500 sm:table-cell">{{ medicine.batch_expiry_date }}</td>
                                <td class="hidden px-3 py-5 text-right text-sm text-gray-500 sm:table-cell">{{ medicine.medicine_unit_price }}</td>
                                <td class="py-5 pl-3 pr-4 text-right text-sm text-gray-500 sm:pr-0">{{ medicine.medicine_total_price }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="hidden pl-4 pr-3 pt-4 text-right text-sm font-semibold sm:table-cell sm:pl-0">Total</th>
                                <th class="pl-6 pr-3 pt-4 text-left text-sm font-semibold sm:hidden">Total</th>
                                <td class="pl-3 pr-4 pt-4 text-right text-sm font-semibold sm:pr-0">-</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            `;
            template.appendChild(productSummaryUpdate);
        }

    // Clone the template
    const newItem = template.cloneNode(true);

    // Remove id attributes to avoid duplicates
    //newItem.querySelectorAll('[id]').forEach(element => element.removeAttribute('id'));

    // Clear values in cloned item
    newItem.querySelectorAll('input, select').forEach(field => field.value = '');

    // Reset stock level, product summary, and other specific elements
    const stockLevelSpan = newItem.querySelector('.stock-level-update');
    const productSummaryContainer = newItem.querySelector('#product_summary-update tbody');
    const totalAmountCell = newItem.querySelector('tfoot td:last-child');
    const batchNumberError = newItem.querySelector('#batch-number-error-update');

    if (stockLevelSpan) stockLevelSpan.textContent = '';
    if (productSummaryContainer) productSummaryContainer.innerHTML = '';
    if (totalAmountCell) totalAmountCell.textContent = '$0.00';
    if (batchNumberError) {
        batchNumberError.textContent = '';
        batchNumberError.classList.add('hidden');
    }

    // Append the new item to the container
    container.appendChild(newItem);

    // Add event listener to the remove button
    const removeButton = newItem.querySelector('.remove-button-update');
    removeButton.addEventListener('click', removeItemUpdate);
    
    // Reinitialize Flatpickr if necessary
    initializeFlatpickr();

    // Update remove button visibility
    toggleRemoveButtonsUpdate();
}

function toggleRemoveButtonsUpdate() {
    const items = document.querySelectorAll('.item__template_update');
    items.forEach((item, index) => {
        const removeButton = item.querySelector('.remove-button-update');
        removeButton.style.display = items.length > 1 ? 'flex' : 'none';
    });
}

function removeItemUpdate(event) {
    event.preventDefault();  // Prevent default button behavior
    const item = event.target.closest('.item__template_update');
    if (item) {
        item.remove();
        toggleRemoveButtonsUpdate();  // Adjust button visibility after removal
    }
}

// Initial call to set up the remove button visibility on page load
document.addEventListener('DOMContentLoaded', () => {
    toggleRemoveButtonsUpdate();
});
