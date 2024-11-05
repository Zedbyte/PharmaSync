document.addEventListener('DOMContentLoaded', function() {
    const entryForm = document.getElementById('entryForm');
    const customEntryInput = document.getElementById('customEntryInput');

    customEntryInput.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            entryForm.submit();
        }
    });
});
