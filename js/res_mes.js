function sendMessage() {
    const message = document.getElementById("userMessage").value;
    const captchaResponse = grecaptcha.getResponse();

    if (!message) {
        alert("Message cannot be empty.");
        return;
    }

    if (!captchaResponse) {
        alert("Please complete the CAPTCHA.");
        return;
    }

    // Verify CAPTCHA with backend (simulated here)
    fetch('https://www.google.com/recaptcha/api/siteverify', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `secret=6LfsRfcqAAAAAOkg5nxKxppbpHUn5KO7KDZwYAup&response=${captchaResponse}`,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Save the message to localStorage (simulate backend storage)
            let messages = JSON.parse(localStorage.getItem("messages")) || [];
            messages.push({ from: "User", text: message });
            localStorage.setItem("messages", JSON.stringify(messages));

            alert("Your message has been sent!");
            document.getElementById("userMessage").value = "";

            // Simulate notifying admin
            localStorage.setItem("adminNotification", "true");

            // Reset CAPTCHA
            grecaptcha.reset();
        } else {
            alert("CAPTCHA verification failed. Please try again.");
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("An error occurred. Please try again.");
    });
}