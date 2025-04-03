document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const pendingRequestsEl = document.getElementById('pending-requests');
    const SCRIPT_URL = 'YOUR_GOOGLE_SCRIPT_WEB_APP_URL'; // Replace with your deployed Google Apps Script URL

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
                booking.requestDateTime = new Date().toISOString();
            }
            return booking;
        });
        localStorage.setItem('bookings', JSON.stringify(bookings));

        pendingRequestsEl.innerHTML = ''; // Clear the list

        // Sort pending requests by requestDateTime (newest first)
        bookings
            .filter(booking => booking.status === 'pending')
            .sort((a, b) => new Date(b.requestDateTime) - new Date(a.requestDateTime))
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
                        <p>Request Sent: ${new Date(booking.requestDateTime).toLocaleString()}</p>
                    </div>
                    <div class="booking-actions">
                        <button class="accept-btn">Accept</button>
                        <button class="reschedule-btn">Reschedule</button>
                        <button class="reject-btn">Reject</button>
                    </div>
                `;

                // Add action listeners
                bookingEl.querySelector('.accept-btn').addEventListener('click', async () => {
                    if (isTimeSlotAvailable(booking.date, booking.time)) {
                        await updateBooking(booking.id, 'accepted');
                    } else {
                        alert('This time slot is already booked or conflicts with another appointment. Please choose a different time or reschedule.');
                    }
                });
                bookingEl.querySelector('.reschedule-btn').addEventListener('click', () => rescheduleBooking(booking));
                bookingEl.querySelector('.reject-btn').addEventListener('click', async () => {
                    await updateBooking(booking.id, 'rejected');
                });

                pendingRequestsEl.appendChild(bookingEl);
            });

        // Add accepted events to calendar
        calendar.removeAllEvents(); // Clear existing events first
        bookings.forEach((booking) => {
            if (booking.status === 'accepted') {
                calendar.addEvent({
                    title: booking.name,
                    start: `${booking.date}T${booking.time}`,
                    extendedProps: {
                        duration: 30 // Assuming each booking is 30 minutes
                    }
                });
            }
        });
    }

    // Check if time slot is available (with 30-minute buffer)
    function isTimeSlotAvailable(date, time) {
        const bookings = JSON.parse(localStorage.getItem('bookings')) || [];
        const selectedDateTime = new Date(`${date}T${time}`);
        const selectedEndTime = new Date(selectedDateTime.getTime() + 30 * 60000); // Add 30 minutes
        
        // Check against all accepted bookings
        const conflicts = bookings.filter(booking => {
            if (booking.status !== 'accepted') return false;
            
            const bookingDateTime = new Date(`${booking.date}T${booking.time}`);
            const bookingEndTime = new Date(bookingDateTime.getTime() + 30 * 60000);
            
            // Check if the new booking overlaps with existing booking
            return (
                (selectedDateTime >= bookingDateTime && selectedDateTime < bookingEndTime) ||
                (selectedEndTime > bookingDateTime && selectedEndTime <= bookingEndTime) ||
                (selectedDateTime <= bookingDateTime && selectedEndTime >= bookingEndTime)
            );
        });
        
        return conflicts.length === 0;
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
            requestDateTime: new Date().toISOString(),
        };
        bookings.push(newBooking);
        localStorage.setItem('bookings', JSON.stringify(bookings));
        loadBookings();
    }

    // Update booking status and send email
    async function updateBooking(id, status) {
        const bookings = JSON.parse(localStorage.getItem('bookings')) || [];
        const updatedBookings = bookings.map((b) => {
            if (b.id === id) {
                return { ...b, status };
            }
            return b;
        });
        localStorage.setItem('bookings', JSON.stringify(updatedBookings));
        
        // Send email notification
        const booking = bookings.find(b => b.id === id);
        if (booking) {
            await sendNotificationEmail(booking, status);
        }
        
        loadBookings();
    }

    // Reschedule booking and send email
    async function rescheduleBooking(booking) {
        const newDate = prompt('Enter new date (YYYY-MM-DD):', booking.date);
        const newTime = prompt('Enter new time (HH:MM):', booking.time);
        
        if (newDate && newTime) {
            if (!isTimeSlotAvailable(newDate, newTime)) {
                alert('This time slot is already booked or conflicts with another appointment. Please choose a different time.');
                return;
            }
            
            const bookings = JSON.parse(localStorage.getItem('bookings')) || [];
            const updatedBookings = bookings.map((b) => {
                if (b.id === booking.id) {
                    return { ...b, date: newDate, time: newTime };
                }
                return b;
            });
            localStorage.setItem('bookings', JSON.stringify(updatedBookings));
            
            // Send email notification
            const updatedBooking = { ...booking, date: newDate, time: newTime };
            await sendNotificationEmail(updatedBooking, 'rescheduled');
            
            loadBookings();
        }
    }

    // Send email notification via Google Apps Script
    async function sendNotificationEmail(booking, action) {
        let subject, body;
        
        switch(action) {
            case 'accepted':
                subject = `Appointment Confirmed - ${booking.date} at ${booking.time}`;
                body = `
                    <h2>Your appointment has been confirmed!</h2>
                    <p>Dear ${booking.name},</p>
                    <p>We're pleased to confirm your appointment on ${booking.date} at ${booking.time}.</p>
                    <p>Details: ${booking.details}</p>
                    <p>Thank you!</p>
                `;
                break;
                
            case 'rejected':
                subject = `Appointment Declined - ${booking.date} at ${booking.time}`;
                body = `
                    <h2>Your appointment request has been declined</h2>
                    <p>Dear ${booking.name},</p>
                    <p>We regret to inform you that your appointment request for ${booking.date} at ${booking.time} cannot be accommodated.</p>
                    <p>Please contact us if you'd like to reschedule.</p>
                    <p>Thank you for your understanding.</p>
                `;
                break;
                
            case 'rescheduled':
                subject = `Appointment Rescheduled - ${booking.date} at ${booking.time}`;
                body = `
                    <h2>Your appointment has been rescheduled</h2>
                    <p>Dear ${booking.name},</p>
                    <p>Your appointment has been rescheduled to ${booking.date} at ${booking.time}.</p>
                    <p>Details: ${booking.details}</p>
                    <p>Please contact us if you need to make any changes.</p>
                `;
                break;
        }
        
        try {
            const response = await fetch(SCRIPT_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    recipientEmail: booking.email,
                    subject: subject,
                    body: body
                })
            });
            
            const result = await response.json();
            if (!result.success) {
                console.error('Email sending failed:', result.error);
                alert('Failed to send notification email. Please notify the client manually.');
            }
        } catch (error) {
            console.error('Error sending email:', error);
            alert('Error sending notification email. Please notify the client manually.');
        }
    }

    loadBookings(); // Initial load
});