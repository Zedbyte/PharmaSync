document.getElementById('rack-id').addEventListener('change', async function () {
    const rackID = this.value; // Get selected rack ID
    if (!rackID) return; // Exit if no rack is selected

    try {
        // Fetch rack details from the server
        const response = await fetch(`/rack-details/${rackID}`);

        
        if (!response.ok) {
            throw new Error('Failed to fetch rack details');
        }
        
        const data = await response.json(); // Assuming the response is JSON
        const rackItemDiv = document.querySelector('.rack-item');

        // Update the rack-item div with the fetched data
        rackItemDiv.innerHTML = `
        <span class="icon">
            ${data.rackData.temperature_controlled
                ? `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" id="temperature"><path fill="#5675cb" d="M13,15.28V5.5a1,1,0,0,0-2,0v9.78A2,2,0,0,0,10,17a2,2,0,0,0,4,0A2,2,0,0,0,13,15.28ZM16.5,13V5.5a4.5,4.5,0,0,0-9,0V13a6,6,0,0,0,3.21,9.83A7,7,0,0,0,12,23,6,6,0,0,0,16.5,13Zm-2,7.07a4,4,0,0,1-6.42-2.2,4,4,0,0,1,1.1-3.76,1,1,0,0,0,.3-.71V5.5a2.5,2.5,0,0,1,5,0v7.94a1,1,0,0,0,.3.71,4,4,0,0,1-.28,6Z"></path></svg>`
                : `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18M9 9h6m-6 6h6" />
                </svg>`}
        </span>
        <div>
            <p class="font-semibold">${data.rackData.location}</p>
            <p class="text-sm">
                ${data.rackData.temperature_controlled ? 'Temperature Controlled' : 'Normal Storage'}
            </p>
        </div>
        `;

        // Update classes based on temperature-controlled status
        rackItemDiv.className = `rack-item flex items-center space-x-3 p-3 rounded-lg mt-5 ${
        data.rackData.temperature_controlled
            ? 'bg-blue-200 text-blue-800 dark:bg-blue-700 dark:text-blue-100'
            : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300'
        }`;
    } catch (error) {
        console.error('Error:', error.message);
        alert('Failed to load rack details. Please try again.');
    }
});
