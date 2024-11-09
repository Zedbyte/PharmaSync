// Function to submit all forms with combined data
function submitAllForms(triggeredFormId) {
    const combinedData = new FormData();

    // List of form IDs
    const formIds = ["dateRangeForm", "relativeDateForm", "entryForm", "searchForm"];

    formIds.forEach((formId) => {
        const form = document.getElementById(formId);
        const formData = new FormData(form);

        // Append each field to combinedData
        for (const [key, value] of formData.entries()) {
            combinedData.append(key, value);
        }
    });

    fetch("/purchase-list/filter", {
        method: "POST",
        body: combinedData,
    })
    .then(response => {
        if (response.redirected) {
            window.location.href = response.url; // Redirect to the new URL
        } else {
            return response.json();
        }
    })
    .then(data => {
        console.log("Success:", data);
    })
    .catch(error => {
        console.error("Error:", error.message);
    });
}


document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('.filter-form');
    const customEntryInput = document.getElementById('customEntryInput');

    // Trigger submission when Enter key is pressed in the custom entry input
    customEntryInput.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            submitAllForms("entryForm"); // Call applyFilters when Enter is pressed on custom entry input
        }
    });
});

// Event listener for entry form buttons
document.querySelectorAll("#entryForm button[type='submit']").forEach(button => {
    button.addEventListener("click", function(event) {
        event.preventDefault();
        
        // Set the value of customEntryInput to the clicked button's value
        document.getElementById("customEntryInput").value = this.value;

        // Submit the form with updated value
        submitAllForms("entryForm");
    });
});

// Event listeners for other forms
document.getElementById("dateRangeForm").addEventListener("submit", function(event) {
    event.preventDefault();
    submitAllForms("dateRangeForm");
});

// document.getElementById("relativeDateForm").addEventListener("submit", function(event) {
//     event.preventDefault();
//     submitAllForms("relativeDateForm");
// });

// Select all radio buttons
const radios = document.querySelectorAll('input[name="relativeDate"]');
radios.forEach((radio) => {
    // Add change event listener
    radio.addEventListener('change', function() {
        // Submit the form when a radio button is clicked
        event.preventDefault();
        submitAllForms("relativeDateForm");
    });
});

document.getElementById("entryForm").addEventListener("submit", function(event) {
    event.preventDefault();
    submitAllForms("entryForm");
});

document.getElementById("searchForm").addEventListener("submit", function(event) {
    event.preventDefault();
    submitAllForms("searchForm");
});
