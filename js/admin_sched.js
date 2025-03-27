document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const pendingRequestsEl = document.getElementById('pending-requests');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: [],
    });
    calendar.render();

    // Load bookings from localStorage
    function loadBookings() {
        let bookings = JSON.parse(localStorage.getItem('bookings')) || [];

        // Ensure all bookings have a requestDateTime field
        bookings = bookings.map((booking) => {
            if (!booking.requestDateTime) {
                booking.requestDateTime = new Date().toLocaleString();
            }
            return booking;
        });
        localStorage.setItem('bookings', JSON.stringify(bookings));

        pendingRequestsEl.innerHTML = ''; // Clear the list

        bookings
            .filter(booking => booking.status === 'pending')
            .sort((a, b) => new Date(b.date + 'T' + b.time) - new Date(a.date + 'T' + a.time))
            .forEach((booking) => {
                const bookingEl = document.createElement('div');
                bookingEl.className = 'booking-item';
                bookingEl.innerHTML = `
                            <div class="booking-details">
                                <h3>${booking.name}</h3>
                                <p>Email: ${booking.email}</p>
                                <p>Date: ${booking.date}</p>
                                <p>Time: ${booking.time}</p>
                                <p>Details: ${booking.details}</p>
                                <p>Request Sent: ${booking.requestDateTime}</p>
                            </div>
                            <div class="booking-actions">
                                <button class="accept-btn">Accept</button>
                                <button class="reschedule-btn">Reschedule</button>
                                <button class="reject-btn">Reject</button>
                            </div>
                        `;

                // Add action listeners
                bookingEl.querySelector('.accept-btn').addEventListener('click', () => updateBooking(booking.id, 'accepted'));
                bookingEl.querySelector('.reschedule-btn').addEventListener('click', () => rescheduleBooking(booking));
                bookingEl.querySelector('.reject-btn').addEventListener('click', () => updateBooking(booking.id, 'rejected'));

                pendingRequestsEl.appendChild(bookingEl);
            });

        // Add accepted events to calendar
        bookings.forEach((booking) => {
            if (booking.status === 'accepted') {
                calendar.addEvent({
                    title: booking.name,
                    start: `${booking.date}T${booking.time}`,
                });
            }
        });
    }

    // Add a new booking
    function addBooking(name, email, date, time, details) {
        const bookings = JSON.parse(localStorage.getItem('bookings')) || [];
        const newBooking = {
            id: Date.now(),
            name,
            email,
            date,
            time,
            details,
            status: 'pending',
            requestDateTime: new Date().toLocaleString(),
        };
        bookings.push(newBooking);
        localStorage.setItem('bookings', JSON.stringify(bookings));
        loadBookings();
    }

    // Update booking status
    function updateBooking(id, status) {
        const bookings = JSON.parse(localStorage.getItem('bookings')) || [];
        const updatedBookings = bookings.map((b) => {
            if (b.id === id) {
                return { ...b, status };
            }
            return b;
        });
        localStorage.setItem('bookings', JSON.stringify(updatedBookings));
        loadBookings();
    }

    // Reschedule booking
    function rescheduleBooking(booking) {
        const newDate = prompt('Enter new date (YYYY-MM-DD):', booking.date);
        const newTime = prompt('Enter new time (HH:MM):', booking.time);
        if (newDate && newTime) {
            const bookings = JSON.parse(localStorage.getItem('bookings')) || [];
            const updatedBookings = bookings.map((b) => {
                if (b.id === booking.id) {
                    return { ...b, date: newDate, time: newTime, requestDateTime: b.requestDateTime || new Date().toLocaleString() };
                }
                return b;
            });
            localStorage.setItem('bookings', JSON.stringify(updatedBookings));
            loadBookings();
        }
    }

    loadBookings(); // Initial load
});