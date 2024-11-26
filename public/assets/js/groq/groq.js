document.getElementById('groqForm').addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent the default form submission

    const groqRequest = document.getElementById('groqRequest').value; // Get the input value
    const responseDiv = document.getElementById('responseContent'); // Get the response display area
    const userPrompt = document.getElementById('userPrompt');
    responseDiv.innerText = ''; // Clear previous content

    // Send the user's query to the backend
    fetch('/ask-groq', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded', // Form-urlencoded request
        },
        body: new URLSearchParams({ groq_request: groqRequest }), // Send user query
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json(); // Expect JSON response from PHP
        })
        .then((data) => {
            if (data.choices && data.choices.length > 0) {
                const responseText = data.choices[0].message.content || 'No content received.';
                console.log('Response Text:', responseText);

                // Parse and replace **bold** syntax with <strong> tags
                const parsedText = parseBoldAndNewLines(responseText);

                groqRequest.value = '';
                typeText(userPrompt, groqRequest);
                typeHTML(responseDiv, parsedText); // Call typing effect
            } else {
                responseDiv.innerText = data.error || 'Unexpected response structure.';
            }
        })
        .catch((error) => {
            responseDiv.innerText = 'Error: Unable to fetch data from GROQ API.';
            console.error('Error:', error);
        });
});

// Function to parse **bold** syntax and replace new lines with <br>
function parseBoldAndNewLines(text) {
    const boldProcessed = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>'); // Process bold
    return boldProcessed.replace(/\n/g, '<br>'); // Replace new lines with <br>
}

// Function to simulate typing effect
function typeText(element, text, speed = 10) {
    let i = 0;
    let typedText = "";

    function type() {
        if (i < text.length) {
            typedText += text.charAt(i);
            element.innerText = typedText; // Update innerText with the typed content
            i++;
            setTimeout(type, speed); // Call the function recursively to continue typing
        }
    }

    type(); // Start typing
}

// Function to simulate typing effect for HTML content
function typeHTML(element, html, speed = 10) {
    let i = 0;
    let typedHTML = "";

    function type() {
        if (i < html.length) {
            typedHTML += html.charAt(i);
            element.innerHTML = typedHTML; // Update innerHTML with the typed content
            i++;
            setTimeout(type, speed); // Call the function recursively to continue typing
        }
    }

    type(); // Start typing
}