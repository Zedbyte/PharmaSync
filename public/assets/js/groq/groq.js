document.getElementById('groqForm').addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent the default form submission

    const requestInput = document.getElementById('groqRequest');
    const groqRequest = requestInput.value.trim(); // Get the input value
    const responseDiv = document.getElementById('responseContent'); // Get the response display area
    const userPrompt = document.getElementById('userPrompt');
    responseDiv.innerText = ''; // Clear previous content

    if (!groqRequest) {
        responseDiv.innerText = 'Please enter a prompt before sending.';
        return;
    }

    // Send the user's query to the backend
    fetch('/ask-groq', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded', // Form-urlencoded request
        },
        body: new URLSearchParams({ groq_request: groqRequest }), // Send user query
    })
        .then(async (response) => {
            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw new Error(data.error || `HTTP error! status: ${response.status}`);
            }

            return data;
        })
        .then((data) => {
            if (data.choices && data.choices.length > 0) {
                const responseText = data.choices[0].message.content || 'No content received.';
                console.log('Response Text:', responseText);

                // Parse and replace **bold** syntax with <strong> tags
                const parsedText = parseText(responseText);

                requestInput.value = '';
                typeText(userPrompt, groqRequest);
                typeHTML(responseDiv, parsedText); // Call typing effect
            } else {
                responseDiv.innerText = data.error || 'Unexpected response structure.';
            }
        })
        .catch((error) => {
            responseDiv.innerText = error.message || 'Error: Unable to fetch data from GROQ API.';
            console.error('Error:', error);
        });
});

// Function to parse **bold**, triple backticks, tabs, and replace new lines with <br>
function parseText(text) {
    // Process bold syntax (**bold**)
    const boldProcessed = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

    // Process triple backticks (```code```)
    const codeProcessed = boldProcessed.replace(/```([\s\S]*?)```/g, '<pre><code>$1</code></pre>');

    // Process tab-indented text (convert each tab level to nested blockquotes)
    const tabProcessed = codeProcessed.replace(/^(\t+)/gm, (_, tabs) => {
        const level = tabs.length; // Count the number of tabs
        return '<blockquote>'.repeat(level); // Open blockquote for each tab
    }).replace(/(\t+)([^\t]+)/g, '$2</blockquote>'); // Close blockquote after text

    // Replace new lines with <br>, excluding within <pre><code> blocks
    const newlineProcessed = tabProcessed.replace(/(?<!<\/pre>)\n/g, '<br>');

    return newlineProcessed;
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