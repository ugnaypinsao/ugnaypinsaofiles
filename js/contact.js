document.getElementById('contactForm').addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent the form from submitting traditionally

    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const subject = document.getElementById('subject').value || "No Subject";
    const message = document.getElementById('message').value;

    // Create a message object
    const newMessage = {
        from: `${name} (${email})`,
        text: message,
        subject: subject
    };

    // Retrieve existing messages from localStorage
    const messages = JSON.parse(localStorage.getItem('messages')) || [];
    messages.push(newMessage);

    // Save updated messages back to localStorage
    localStorage.setItem('messages', JSON.stringify(messages));

    alert('Message sent successfully!');
    this.reset(); // Reset the form fields
});
