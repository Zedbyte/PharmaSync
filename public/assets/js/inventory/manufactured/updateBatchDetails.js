document.addEventListener('DOMContentLoaded', () => {
    const batchSelect = document.querySelector('#batch-number-id');
    const batchDetailsContainer = document.querySelector('#batch_details .batch-details tbody');

    // Utility to fetch and populate batch details
    const fetchBatchDetails = async (batchId) => {
        try {
            const response = await fetch(`/batch-details/${batchId}`);
            const data = await response.json();
            
            if (data.error) {
                batchDetailsContainer.innerHTML = `
                    <tr class="border-b border-gray-200">
                        <td colspan="5" class="py-5 text-center text-sm text-gray-500 dark:text-gray-100">
                            ${data.error}
                        </td>
                    </tr>
                `;
                return;
            }

            // Populate batch details
            const { batch_id, production_date, rack_id_pk, location, temperature_controlled } = data.batchDetails;

            batchDetailsContainer.innerHTML = `
                <tr class="border-b border-gray-200">
                    <td class="py-5 pl-3 pr-4 text-left text-sm text-gray-500 dark:text-gray-100 sm:pr-0">${batch_id || '-'}</td>
                    <td class="py-5 pl-3 pr-4 text-left text-sm text-gray-500 dark:text-gray-100 sm:pr-0">${production_date || '-'}</td>
                    <td class="py-5 pl-3 pr-4 text-left text-sm text-gray-500 dark:text-gray-100 sm:pr-0">${rack_id_pk || '-'}</td>
                    <td class="hidden px-3 py-5 text-left text-sm text-gray-500 dark:text-gray-100 sm:table-cell">${location || '-'}</td>
                    <td class="py-5 pl-3 pr-4 text-left text-sm text-gray-500 dark:text-gray-100 sm:pr-0">
                        ${temperature_controlled ? 'Yes' : 'No'}
                    </td>
                </tr>
            `;
        } catch (error) {
            console.error('Error fetching batch details:', error);
            batchDetailsContainer.innerHTML = `
                <tr class="border-b border-gray-200">
                    <td colspan="5" class="py-5 text-center text-sm text-gray-500 dark:text-gray-100">
                        Failed to fetch batch details.
                    </td>
                </tr>
            `;
        }
    };

    // Event listener for batch selection
    batchSelect.addEventListener('change', () => {
        const batchId = batchSelect.value;

        if (batchId) {
            // Clear previous details and fetch new ones
            batchDetailsContainer.innerHTML = `
                <tr class="border-b border-gray-200">
                    <td colspan="5" class="py-5 text-center text-sm text-gray-500 dark:text-gray-100">
                        Loading batch details...
                    </td>
                </tr>
            `;
            fetchBatchDetails(batchId);
        }
    });
});
