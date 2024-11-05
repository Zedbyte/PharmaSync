// document.addEventListener('DOMContentLoaded', function() {
//     const forms = document.querySelectorAll('.filter-form');
//     const customEntryInput = document.getElementById('customEntryInput');

//     // Trigger submission when Enter key is pressed in the custom entry input
//     customEntryInput.addEventListener('keypress', function(event) {
//         if (event.key === 'Enter') {
//             event.preventDefault();
//             applyFilters(); // Call applyFilters when Enter is pressed on custom entry input
//         }
//     });

//     // Handle form submission for all filters
//     forms.forEach((form) => {
//         form.addEventListener('submit', function(e) {
//             e.preventDefault(); // Prevent default form submission
//             applyFilters(); // Custom function to submit all filters together
//         });
//     });

//     // Add event listeners to entry buttons
//     const entryButtons = document.querySelectorAll('form#entryForm button[name="entryValue"]');
//     entryButtons.forEach(button => {
//         button.addEventListener('click', function(e) {
//             e.preventDefault(); // Prevent default button behavior
//             // Set the entry value and apply filters
//             const entryValue = this.value; // Use the value of the clicked button
//             applyFilters(entryValue); // Pass the entry value to applyFilters
//         });
//     });

//     function applyFilters(entryValue = null) {
//         const params = new URLSearchParams();

//         // Use the entry value from the button if provided
//         if (entryValue) {
//             params.set('entries', entryValue);
//         } else {
//             // Check custom entry input value
//             const customValue = customEntryInput.value;
//             if (customValue) params.set('entries', customValue);
//         }

//         // Start-End Date filter
//         const startDate = document.querySelector('input[name="start_date"]').value;
//         const endDate = document.querySelector('input[name="end_date"]').value;
//         if (startDate) params.set('start_date', startDate);
//         if (endDate) params.set('end_date', endDate);

//         // Relative Date filter
//         const lastNDate = document.querySelector('input[name="last_n_date"]:checked');
//         if (lastNDate) params.set('last_n_days', lastNDate.value);

//         // Redirect with all filter values as query parameters
//         window.location.href = `/purchase-list?${params.toString()}`;
//     }
// });
