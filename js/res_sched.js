document.getElementById('booking-form').addEventListener('submit', function (e) {
    e.preventDefault();

    const booking = {
        id: Date.now(),
        name: document.getElementById('name').value,
        email: document.getElementById('email').value,
        date: document.getElementById('date').value,
        time: document.getElementById('time').value,
        details: document.getElementById('details').value,
        status: 'pending'
    };

    // Save to localStorage
    const bookings = JSON.parse(localStorage.getItem('bookings')) || [];
    bookings.push(booking);
    localStorage.setItem('bookings', JSON.stringify(bookings));

    // Show popup instead of alert
    showPopup('Booking submitted successfully!');
    this.reset(); // Clear form
});

// Popup functions
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