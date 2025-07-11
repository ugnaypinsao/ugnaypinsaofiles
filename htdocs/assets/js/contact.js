// Show popup function
function showPopup(message) {
    const modal = document.getElementById('popup-modal');
    const messageElement = document.getElementById('popup-message');
    messageElement.textContent = message;
    modal.style.display = 'block';
}

// Close popup when clicking OK button
document.getElementById('ok-button').addEventListener('click', function() {
    document.getElementById('popup-modal').style.display = 'none';
});

// Close popup when clicking outside
window.addEventListener('click', function(event) {
    const modal = document.getElementById('popup-modal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
});

// Contact form submission
document.getElementById('contactForm').addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent the form from submitting traditionally

    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const subject = document.getElementById('subject').value || "No Subject";
    const message = document.getElementById('message').value;

    // Validate required fields
    if (!name || !email || !message) {
        showPopup('Please fill in all required fields.');
        return;
    }

    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showPopup('Please enter a valid email address.');
        return;
    }

    // Create a message object
    const newMessage = {
        from: `${name} (${email})`,
        text: message,
        subject: subject,
        timestamp: new Date().toISOString()
    };

    try {
        // Retrieve existing messages from localStorage
        const messages = JSON.parse(localStorage.getItem('messages')) || [];
        messages.push(newMessage);

        // Save updated messages back to localStorage
        localStorage.setItem('messages', JSON.stringify(messages));

        // Show success popup
        showPopup('Message sent successfully! We will get back to you soon.');
        
        // Reset the form fields
        this.reset();
    } catch (error) {
        showPopup('An error occurred while sending your message. Please try again.');
        console.error('Error saving message:', error);
    }
});
