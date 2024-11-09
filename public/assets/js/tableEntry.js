// document.addEventListener('DOMContentLoaded', () => {
//     const entryDropdownButton = document.getElementById('entryDropdownButton');
//     const entryDropdownMenu = document.getElementById('entryDropdownMenu');
//     const selectedEntry = document.getElementById('selectedEntry');
//     const customEntryInput = document.getElementById('customEntryInput');
//     const predefinedEntries = entryDropdownMenu.querySelectorAll('button[data-value]');

//     predefinedEntries.forEach(entry => {
//         entry.addEventListener('click', () => {
//             const value = entry.getAttribute('data-value');
//             selectedEntry.textContent = `${value} entries`;
//             entryDropdownButton.dataset.selectedValue = value; // Attach selected value for MVC Controller
//         });
//     });

//     customEntryInput.addEventListener('change', () => {
//         const customValue = customEntryInput.value;
//         if (customValue) {
//             selectedEntry.textContent = `${customValue} entries`;
//             entryDropdownButton.dataset.selectedValue = customValue; // Attach custom value for MVC Controller
//         }
//     });
// });