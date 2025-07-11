document.addEventListener('DOMContentLoaded', function() {
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
            <button class="tab-btn" data-tab="rescheduled">Rescheduled (0)</button>
        </div>
        <div id="accepted-summary" class="summary-content active">
            <ul class="summary-list"></ul>
        </div>
        <div id="rejected-summary" class="summary-content">
            <ul class="summary-list"></ul>
        </div>
        <div id="rescheduled-summary" class="summary-content">
            <ul class="summary-list"></ul>
        </div>
    `;
    containerEl.appendChild(summarySection);

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: [],
        eventClick: function(info) {
            const bookings = JSON.parse(localStorage.getItem('bookings')) || [];
            const booking = bookings.find(b => 
                b.id === info.event.extendedProps.id && 
                (b.status === 'accepted' || b.status === 'rescheduled')
            );
            
            if (booking) {
                const appointmentDate = new Date(`${booking.date}T${booking.time}`);
                const currentDate = new Date();
                
                if (appointmentDate < currentDate) {
                    showAlert('Past Appointment', 'This appointment has already passed and cannot be modified.', 'info');
                    return;
                }
                
                showBookingDetails(booking, info.event);
            } else {
                showAlert('Error', 'Booking not found', 'error');
            }
            info.jsEvent.preventDefault();
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

    // Alert modal function
    function showAlert(title, message, type = 'info') {
        const modal = document.createElement('div');
        modal.className = 'alert-modal';
        modal.innerHTML = `
            <div class="alert-content ${type}">
                <span class="close-alert">&times;</span>
                <h2>${title}</h2>
                <div class="alert-message">${message}</div>
                <div class="alert-actions">
                    <button class="confirm-alert">OK</button>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        const closeModal = () => document.body.removeChild(modal);
        
        modal.querySelector('.close-alert').addEventListener('click', closeModal);
        modal.querySelector('.confirm-alert').addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });
    }

    // Confirmation modal function
    function showConfirm(title, message, confirmCallback, cancelCallback, type = 'info') {
        const modal = document.createElement('div');
        modal.className = 'alert-modal';
        modal.innerHTML = `
            <div class="alert-content ${type}">
                <span class="close-alert">&times;</span>
                <h2>${title}</h2>
                <div class="alert-message">${message}</div>
                <div class="alert-actions">
                    <button class="confirm-btn">Confirm</button>
                    <button class="cancel-btn">Cancel</button>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        const closeModal = () => document.body.removeChild(modal);
        
        modal.querySelector('.close-alert').addEventListener('click', () => {
            if (cancelCallback) cancelCallback();
            closeModal();
        });
        
        modal.querySelector('.confirm-btn').addEventListener('click', () => {
            if (confirmCallback) confirmCallback();
            closeModal();
        });
        
        modal.querySelector('.cancel-btn').addEventListener('click', () => {
            if (cancelCallback) cancelCallback();
            closeModal();
        });
        
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                if (cancelCallback) cancelCallback();
                closeModal();
            }
        });
    }

    // Prompt modal function
    function showPrompt(title, message, defaultValue, confirmCallback, cancelCallback) {
        const modal = document.createElement('div');
        modal.className = 'alert-modal';
        modal.innerHTML = `
            <div class="alert-content">
                <span class="close-alert">&times;</span>
                <h2>${title}</h2>
                <div class="alert-message">${message}</div>
                <input type="text" class="prompt-input" value="${defaultValue || ''}" placeholder="${message}">
                <div class="alert-actions">
                    <button class="confirm-btn">OK</button>
                    <button class="cancel-btn">Cancel</button>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        const inputField = modal.querySelector('.prompt-input');
        inputField.focus();
        inputField.select();

        const closeModal = () => document.body.removeChild(modal);
        
        modal.querySelector('.close-alert').addEventListener('click', () => {
            if (cancelCallback) cancelCallback(null);
            closeModal();
        });
        
        modal.querySelector('.confirm-btn').addEventListener('click', () => {
            const value = inputField.value.trim();
            if (confirmCallback) confirmCallback(value);
            closeModal();
        });
        
        modal.querySelector('.cancel-btn').addEventListener('click', () => {
            if (cancelCallback) cancelCallback(null);
            closeModal();
        });
        
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                if (cancelCallback) cancelCallback(null);
                closeModal();
            }
        });
        
        inputField.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                const value = inputField.value.trim();
                if (confirmCallback) confirmCallback(value);
                closeModal();
            }
        });
    }

    // Show booking details with past appointment check
    function showBookingDetails(booking, calendarEvent = null) {
        const modal = document.createElement('div');
        modal.className = 'booking-modal';
        
        const showActions = calendarEvent !== null;
        const appointmentDate = new Date(`${booking.date}T${booking.time}`);
        const currentDate = new Date();
        const isPastAppointment = appointmentDate < currentDate;
        
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
                    ${booking.rescheduleReason ? `<p><strong>Reschedule Reason:</strong> ${booking.rescheduleReason}</p>` : ''}
                    ${booking.originalDate ? `<p><strong>Originally Scheduled:</strong> ${booking.originalDate} at ${booking.originalTime}</p>` : ''}
                    <p><strong>Request Sent:</strong> ${new Date(booking.requestDateTime).toLocaleString()}</p>
                    ${isPastAppointment ? `<p class="past-appointment-warning">This appointment has already passed</p>` : ''}
                </div>
                ${showActions && !isPastAppointment && (booking.status === 'accepted' || booking.status === 'rescheduled') ? `
                <div class="modal-actions">
                    <button class="reschedule-event-btn">Reschedule</button>
                    <button class="cancel-event-btn">${booking.status === 'rescheduled' ? 'Cancel Rescheduled Booking' : 'Cancel Booking'}</button>
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

        if (showActions && !isPastAppointment && (booking.status === 'accepted' || booking.status === 'rescheduled')) {
            modal.querySelector('.reschedule-event-btn').addEventListener('click', () => {
                document.body.removeChild(modal);
                rescheduleBooking(booking, calendarEvent);
            });

            modal.querySelector('.cancel-event-btn').addEventListener('click', () => {
                document.body.removeChild(modal);
                showPrompt(
                    'Cancellation Reason', 
                    'Please enter the reason for cancellation:', 
                    '',
                    (reason) => {
                        if (reason) {
                            sendGmail(booking.email, 'Booking Cancelled', 
                                `Dear ${booking.name},\n\nYour booking for ${booking.date} at ${booking.time} has been cancelled.\n\nReason: ${reason}\n\nPlease contact us if you have any questions.\n\nBest regards,\n[Your Business Name]`);
                            updateBooking(booking.id, 'cancelled', reason);
                        }
                    }
                );
            });
        }
    }

    // Create summary list item
    function createSummaryListItem(booking) {
        const li = document.createElement('li');
        li.className = `summary-item ${booking.status}`;
        
        let statusText = booking.status;
        if (booking.status === 'rescheduled') {
            statusText = 'Rescheduled';
        }
        
        li.innerHTML = `
            <div class="summary-item-header">
                <span class="booking-name">${booking.name}</span>
                <span class="booking-date">${booking.date} at ${booking.time}</span>
                <span class="status-badge ${booking.status}">${statusText}</span>
            </div>
            ${booking.status === 'rescheduled' && booking.originalDate ? `
            <div class="rescheduled-info">
                <small>Originally: ${booking.originalDate} at ${booking.originalTime}</small>
            </div>
            ` : ''}
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

        // Count appointments for tabs
        const acceptedCount = bookings.filter(b => b.status === 'accepted').length;
        const rejectedCount = bookings.filter(b => b.status === 'rejected').length;
        const rescheduledCount = bookings.filter(b => b.status === 'rescheduled').length;
        
        document.querySelector('[data-tab="accepted"]').textContent = `Accepted (${acceptedCount})`;
        document.querySelector('[data-tab="rejected"]').textContent = `Rejected (${rejectedCount})`;
        document.querySelector('[data-tab="rescheduled"]').textContent = `Rescheduled (${rescheduledCount})`;

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
                        showAlert('Time Slot Unavailable', 'This time slot is already booked or conflicts with another appointment. Please choose a different time or reschedule.', 'error');
                    }
                });
                
                bookingEl.querySelector('.reschedule-btn').addEventListener('click', () => {
                    showPrompt(
                        'Reschedule Date', 
                        'Enter new date (YYYY-MM-DD):', 
                        booking.date,
                        (newDate) => {
                            if (newDate) {
                                showPrompt(
                                    'Reschedule Time', 
                                    'Enter new time (HH:MM):', 
                                    booking.time,
                                    (newTime) => {
                                        if (newTime) {
                                            showPrompt(
                                                'Reschedule Reason', 
                                                'Please enter the reason for rescheduling:', 
                                                '',
                                                (reason) => {
                                                    if (reason) {
                                                        if (!isTimeSlotAvailable(newDate, newTime)) {
                                                            showAlert('Time Slot Unavailable', 'This time slot is already booked or conflicts with another appointment. Please choose a different time.', 'error');
                                                            return;
                                                        }
                                                        
                                                        const emailBody = `Dear ${booking.name},\n\nYour booking has been rescheduled to ${newDate} at ${newTime}.\n\nReason: ${reason}\n\nPlease let us know if this works for you.\n\nBest regards,\n[Your Business Name]`;
                                                        sendGmail(booking.email, 'Booking Rescheduled', emailBody);
                                                        
                                                        const bookings = JSON.parse(localStorage.getItem('bookings')) || [];
                                                        const updatedBookings = bookings.map((b) => {
                                                            if (b.id === booking.id) {
                                                                return { 
                                                                    ...b, 
                                                                    date: newDate, 
                                                                    time: newTime,
                                                                    originalDate: b.date,
                                                                    originalTime: b.time,
                                                                    rescheduleReason: reason,
                                                                    status: 'rescheduled'
                                                                };
                                                            }
                                                            return b;
                                                        });
                                                        localStorage.setItem('bookings', JSON.stringify(updatedBookings));
                                                        loadBookings();
                                                    }
                                                }
                                            );
                                        }
                                    }
                                );
                            }
                        }
                    );
                });
                
                bookingEl.querySelector('.reject-btn').addEventListener('click', () => {
                    showPrompt(
                        'Rejection Reason', 
                        'Please enter the reason for rejection:', 
                        '',
                        (reason) => {
                            if (reason) {
                                const emailBody = `Dear ${booking.name},\n\nWe regret to inform you that your booking request for ${booking.date} at ${booking.time} has been rejected.\n\nReason: ${reason}\n\nPlease contact us if you have any questions.\n\nBest regards,\n[Your Business Name]`;
                                sendGmail(booking.email, 'Booking Rejected', emailBody);
                                
                                updateBooking(booking.id, 'rejected', reason);
                            }
                        }
                    );
                });

                pendingRequestsEl.appendChild(bookingEl);
            });

        // Process accepted, rejected and rescheduled bookings for summary
        const acceptedList = document.querySelector('#accepted-summary .summary-list');
        const rejectedList = document.querySelector('#rejected-summary .summary-list');
        const rescheduledList = document.querySelector('#rescheduled-summary .summary-list');
        
        // Clear calendar before adding events
        calendar.removeAllEvents();
        
        bookings.forEach((booking) => {
            if (['accepted', 'rejected', 'rescheduled'].includes(booking.status)) {
                const listItem = createSummaryListItem(booking);
                
                if (booking.status === 'accepted') {
                    acceptedList.appendChild(listItem);
                } else if (booking.status === 'rejected') {
                    rejectedList.appendChild(listItem);
                } else if (booking.status === 'rescheduled') {
                    rescheduledList.appendChild(listItem);
                }
            }

            // Add to calendar if accepted or rescheduled
            if (booking.status === 'accepted' || booking.status === 'rescheduled') {
                const appointmentDate = new Date(`${booking.date}T${booking.time}`);
                const currentDate = new Date();
                const isPastAppointment = appointmentDate < currentDate;
                
                calendar.addEvent({
                    id: booking.id.toString(),
                    title: booking.status === 'rescheduled' ? 'Rescheduled' : 'Booked',
                    start: `${booking.date}T${booking.time}`,
                    extendedProps: {
                        id: booking.id,
                        name: booking.name,
                        email: booking.email,
                        details: booking.details,
                        requestDateTime: booking.requestDateTime,
                        reason: booking.reason || '',
                        rescheduleReason: booking.rescheduleReason || '',
                        originalDate: booking.originalDate || '',
                        originalTime: booking.originalTime || '',
                        isPastAppointment: isPastAppointment
                    },
                    color: booking.status === 'rescheduled' ? '#2196F3' : '#5e8d56',
                    textColor: isPastAppointment ? '#999999' : '#ffffff'
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
            if (booking.status !== 'accepted' && booking.status !== 'rescheduled') return false;
            
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

    // Update booking status
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
    function rescheduleBooking(booking, calendarEvent = null) {
        showPrompt(
            'Reschedule Date', 
            'Enter new date (YYYY-MM-DD):', 
            booking.date,
            (newDate) => {
                if (newDate) {
                    showPrompt(
                        'Reschedule Time', 
                        'Enter new time (HH:MM):', 
                        booking.time,
                        (newTime) => {
                            if (newTime) {
                                showPrompt(
                                    'Reschedule Reason', 
                                    'Please enter the reason for rescheduling:', 
                                    '',
                                    (reason) => {
                                        if (reason) {
                                            if (!isTimeSlotAvailable(newDate, newTime)) {
                                                showAlert('Time Slot Unavailable', 'This time slot is already booked or conflicts with another appointment. Please choose a different time.', 'error');
                                                return;
                                            }
                                            
                                            const emailBody = `Dear ${booking.name},\n\nYour booking has been rescheduled to ${newDate} at ${newTime}.\n\nReason: ${reason}\n\nPlease let us know if this works for you.\n\nBest regards,\n[Your Business Name]`;
                                            sendGmail(booking.email, 'Booking Rescheduled', emailBody);
                                            
                                            const bookings = JSON.parse(localStorage.getItem('bookings')) || [];
                                            const updatedBookings = bookings.map((b) => {
                                                if (b.id === booking.id) {
                                                    return { 
                                                        ...b, 
                                                        date: newDate, 
                                                        time: newTime,
                                                        originalDate: b.originalDate || b.date,
                                                        originalTime: b.originalTime || b.time,
                                                        rescheduleReason: reason,
                                                        status: 'rescheduled'
                                                    };
                                                }
                                                return b;
                                            });
                                            localStorage.setItem('bookings', JSON.stringify(updatedBookings));
                                            
                                            if (calendarEvent) {
                                                calendarEvent.remove();
                                            }
                                            
                                            loadBookings();
                                        }
                                    }
                                );
                            }
                        }
                    );
                }
            }
        );
    }

    loadBookings();
});