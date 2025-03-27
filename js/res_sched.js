
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

    alert('Booking submitted successfully!');
    this.reset(); // Clear form
});
