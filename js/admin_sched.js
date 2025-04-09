document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const pendingRequestsEl = document.getElementById('pending-requests');
    const containerEl = document.querySelector('.container');

    // Create summary section
    const summarySection = document.createElement('div');
    summarySection.className = 'summary-section';
    summarySection.innerHTML = `
        <h2>Appointment Summary</h2>
        <div class="summary-tabs">
            <button class="tab-btn active" data-tab="accepted">Accepted (0)</button>
            <button class="tab-btn" data-tab="rejected">Rejected (0)</button>
        </div>
        <div id="accepted-summary" class="summary-content active">
            <ul class="summary-list"></ul>
        </div>
        <div id="rejected-summary" class="summary-content">
            <ul class="summary-list"></ul>
        </div>
    `;
    containerEl.appendChild(summarySection);

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: [],
        eventClick: function(info) {
            showBookingDetails(info.event);
        }
    });
    calendar.render();

    // Tab functionality
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.summary-content').forEach(c => c.classList.remove('active'));
            
            btn.classList.add('active');
            document.getElementById(`${btn.dataset.tab}-summary`).classList.add('active');
        });
    });

    // Function to show booking details in a modal
    function showBookingDetails(booking) {
        const modal = document.createElement('div');
        modal.className = 'booking-modal';
        modal.innerHTML = `
            <div class="modal-content">
                <span class="close-modal">&times;</span>
                <h2>Booking Details</h2>
                <div class="booking-info">
                    <p><strong>Name:</strong> ${booking.name}</p>
                    <p><strong>Email:</strong> ${booking.email}</p>
                    <p><strong>Date:</strong> ${booking.date}</p>
                    <p><strong>Time:</strong> ${booking.time}</p>
                    <p><strong>Details:</strong> ${booking.details}</p>
                    <p><strong>Status:</strong> ${booking.status}</p>
                    ${booking.reason ? `<p><strong>Reason:</strong> ${booking.reason}</p>` : ''}
                    <p><strong>Request Sent:</strong> ${new Date(booking.requestDateTime).toLocaleString()}</p>
                </div>
                ${booking.status === 'accepted' ? `
                <div class="modal-actions">
                    <button class="reschedule-event-btn">Reschedule</button>
                    <button class="cancel-event-btn">Cancel Booking</button>
                </div>
                ` : ''}
            </div>
        `;

        document.body.appendChild(modal);

        modal.querySelector('.close-modal').addEventListener('click', () => {
            document.body.removeChild(modal);
        });

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                document.body.removeChild(modal);
            }
        });

        if (booking.status === 'accepted') {
            modal.querySelector('.reschedule-event-btn').addEventListener('click', () => {
                document.body.removeChild(modal);
                rescheduleBooking(booking);
            });

            modal.querySelector('.cancel-event-btn').addEventListener('click', () => {
                document.body.removeChild(modal);
                const reason = prompt('Please enter the reason for cancellation:');
                if (reason !== null) {
                    sendGmail(booking.email, 'Booking Cancelled', 
                        `Dear ${booking.name},\n\nYour booking for ${booking.date} at ${booking.time} has been cancelled.\n\nReason: ${reason}\n\nPlease contact us if you have any questions.\n\nBest regards,\n[Your Business Name]`);
                    updateBooking(booking.id, 'cancelled', reason);
                }
            });
        }
    }

    // Helper function to find booking by event
    function getBookingByEvent(event) {
        const bookings = JSON.parse(localStorage.getItem('bookings')) || [];
        return bookings.find(b => 
            b.name === event.title && 
            b.date === event.startStr.split('T')[0] && 
            b.time === event.startStr.split('T')[1].substring(0, 5)
        );
    }

    // Create summary list item
    function createSummaryListItem(booking) {
        const li = document.createElement('li');
        li.className = `summary-item ${booking.status}`;
        li.innerHTML = `
            <div class="summary-item-header">
                <span class="booking-name">${booking.name}</span>
                <span class="booking-date">${booking.date} at ${booking.time}</span>
                <span class="status-badge ${booking.status}">${booking.status}</span>
            </div>
        `;
        
        li.addEventListener('click', () => {
            showBookingDetails(booking);
        });
        
        return li;
    }

    // Load bookings from localStorage
    function loadBookings() {
        let bookings = JSON.parse(localStorage.getItem('bookings')) || [];

        bookings = bookings.map((booking) => {
            if (!booking.requestDateTime) {
                booking.requestDateTime = new Date().toISOString();
            }
            return booking;
        });
        localStorage.setItem('bookings', JSON.stringify(bookings));

        pendingRequestsEl.innerHTML = '';
        document.querySelectorAll('.summary-list').forEach(list => list.innerHTML = '');

        // Count accepted/rejected for tabs
        const acceptedCount = bookings.filter(b => b.status === 'accepted').length;
        const rejectedCount = bookings.filter(b => b.status === 'rejected').length;
        
        document.querySelector('[data-tab="accepted"]').textContent = `Accepted (${acceptedCount})`;
        document.querySelector('[data-tab="rejected"]').textContent = `Rejected (${rejectedCount})`;

        // Sort all bookings by date (newest first)
        bookings.sort((a, b) => new Date(b.date + 'T' + b.time) - new Date(a.date + 'T' + a.time));

        // Process pending requests
        bookings
            .filter(booking => booking.status === 'pending')
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

                bookingEl.querySelector('.accept-btn').addEventListener('click', () => {
                    if (isTimeSlotAvailable(booking.date, booking.time)) {
                        sendGmail(booking.email, 'Booking Accepted', 
                            `Dear ${booking.name},\n\nYour booking for ${booking.date} at ${booking.time} has been accepted.\n\nLooking forward to seeing you!\n\nBest regards,\n[Your Business Name]`);
                        updateBooking(booking.id, 'accepted');
                    } else {
                        alert('This time slot is already booked or conflicts with another appointment. Please choose a different time or reschedule.');
                    }
                });
                
                bookingEl.querySelector('.reschedule-btn').addEventListener('click', () => {
                    const newDate = prompt('Enter new date (YYYY-MM-DD):', booking.date);
                    const newTime = prompt('Enter new time (HH:MM):', booking.time);
                    
                    if (newDate && newTime) {
                        if (!isTimeSlotAvailable(newDate, newTime)) {
                            alert('This time slot is already booked or conflicts with another appointment. Please choose a different time.');
                            return;
                        }
                        
                        sendGmail(booking.email, 'Booking Rescheduled', 
                            `Dear ${booking.name},\n\nYour booking has been rescheduled to ${newDate} at ${newTime}.\n\nPlease let us know if this works for you.\n\nBest regards,\n[Your Business Name]`);
                        
                        const bookings = JSON.parse(localStorage.getItem('bookings')) || [];
                        const updatedBookings = bookings.map((b) => {
                            if (b.id === booking.id) {
                                return { ...b, date: newDate, time: newTime };
                            }
                            return b;
                        });
                        localStorage.setItem('bookings', JSON.stringify(updatedBookings));
                        loadBookings();
                    }
                });
                
                bookingEl.querySelector('.reject-btn').addEventListener('click', () => {
                    const reason = prompt('Please enter the reason for rejection:');
                    if (reason !== null) {
                        sendGmail(booking.email, 'Booking Rejected', 
                            `Dear ${booking.name},\n\nWe regret to inform you that your booking request for ${booking.date} at ${booking.time} has been rejected.\n\nReason: ${reason}\n\nPlease contact us if you have any questions.\n\nBest regards,\n[Your Business Name]`);
                        updateBooking(booking.id, 'rejected', reason);
                    }
                });

                pendingRequestsEl.appendChild(bookingEl);
            });

        // Process accepted and rejected bookings for summary
        const acceptedList = document.querySelector('#accepted-summary .summary-list');
        const rejectedList = document.querySelector('#rejected-summary .summary-list');
        
        bookings.forEach((booking) => {
            if (booking.status === 'accepted' || booking.status === 'rejected') {
                const listItem = createSummaryListItem(booking);
                
                if (booking.status === 'accepted') {
                    acceptedList.appendChild(listItem);
                } else if (booking.status === 'rejected') {
                    rejectedList.appendChild(listItem);
                }
            }

            // Add to calendar if accepted
            if (booking.status === 'accepted') {
                calendar.addEvent({
                    title: booking.name,
                    start: `${booking.date}T${booking.time}`,
                    extendedProps: {
                        email: booking.email,
                        details: booking.details,
                        requestDateTime: booking.requestDateTime,
                        reason: booking.reason || ''
                    }
                });
            }
        });
    }

    // Function to open Gmail compose window
    function sendGmail(to, subject, body) {
        const gmailUrl = `https://mail.google.com/mail/?view=cm&fs=1&to=${encodeURIComponent(to)}&su=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}&ui=2&tf=1`;
        window.open(gmailUrl, '_blank', 'width=800,height=600');
    }

    // Check if time slot is available
    function isTimeSlotAvailable(date, time) {
        const bookings = JSON.parse(localStorage.getItem('bookings')) || [];
        const selectedDateTime = new Date(`${date}T${time}`);
        const selectedEndTime = new Date(selectedDateTime.getTime() + 30 * 60000);
        
        const conflicts = bookings.filter(booking => {
            if (booking.status !== 'accepted') return false;
            
            const bookingDateTime = new Date(`${booking.date}T${booking.time}`);
            const bookingEndTime = new Date(bookingDateTime.getTime() + 30 * 60000);
            
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

    // Update booking status (now includes reason parameter)
    function updateBooking(id, status, reason = '') {
        const bookings = JSON.parse(localStorage.getItem('bookings')) || [];
        const updatedBookings = bookings.map((b) => {
            if (b.id === id) {
                return { ...b, status, reason };
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
            if (!isTimeSlotAvailable(newDate, newTime)) {
                alert('This time slot is already booked or conflicts with another appointment. Please choose a different time.');
                return;
            }
            
            sendGmail(booking.email, 'Booking Rescheduled', 
                `Dear ${booking.name},\n\nYour booking has been rescheduled to ${newDate} at ${newTime}.\n\nPlease let us know if this works for you.\n\nBest regards,\n[Your Business Name]`);
            
            const bookings = JSON.parse(localStorage.getItem('bookings')) || [];
            const updatedBookings = bookings.map((b) => {
                if (b.id === booking.id) {
                    return { ...b, date: newDate, time: newTime };
                }
                return b;
            });
            localStorage.setItem('bookings', JSON.stringify(updatedBookings));
            loadBookings();
        }
    }

    loadBookings();
});