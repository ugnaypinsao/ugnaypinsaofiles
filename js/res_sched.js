document.getElementById('booking-form').addEventListener('submit', function (e) {
    e.preventDefault();

    // Get form values
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const date = document.getElementById('date').value;
    const time = document.getElementById('time').value;
    const details = document.getElementById('details').value;

    // Format date for display
    const formattedDate = new Date(date).toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    // Format time for display
    const formattedTime = new Date(`2000-01-01T${time}`).toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    });

    // Create booking object
    const booking = {
        id: Date.now(),
        name: name,
        email: email,
        date: date,
        time: time,
        details: details,
        status: 'pending',
        createdAt: new Date().toISOString()
    };

    // Save to localStorage
    const bookings = JSON.parse(localStorage.getItem('bookings')) || [];
    bookings.push(booking);
    localStorage.setItem('bookings', JSON.stringify(bookings));

    // Show success modal with details
    showSuccessModal(
        `Thank you, ${name}! Your appointment request has been submitted successfully. We've sent a confirmation to ${email}.`,
        formattedDate,
        formattedTime
    );

    // Reset form
    this.reset();
});

function showSuccessModal(message, date, time) {
    const modal = document.getElementById('popup-modal');
    const messageElement = document.getElementById('popup-message');
    const dateElement = document.getElementById('summary-date');
    const timeElement = document.getElementById('summary-time');

    messageElement.textContent = message;
    dateElement.textContent = date;
    timeElement.textContent = time;

    modal.style.display = 'block';
    document.body.style.overflow = 'hidden'; // Prevent scrolling when modal is open
}

// Close modal when clicking OK button
document.getElementById('ok-button').addEventListener('click', function() {
    closeModal();
});

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const modal = document.getElementById('popup-modal');
    if (event.target === modal) {
        closeModal();
    }
});

function closeModal() {
    const modal = document.getElementById('popup-modal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto'; // Re-enable scrolling
}

// Improve date input UX
document.getElementById('date').addEventListener('focus', function() {
    this.type = 'date';
    if (!this.value) {
        // Set min date to today
        const today = new Date().toISOString().split('T')[0];
        this.min = today;
    }
});

// Improve time input UX
document.getElementById('time').addEventListener('focus', function() {
    this.type = 'time';
});