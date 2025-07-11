<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin: Schedule Management</title>
    <link rel="stylesheet" href="../assets/css/admin_sched.css">
</head>

<body>
    <div class="container">
        <h1><a href="index.php">Manage Booking-Appointments</a></h1>
        <div id="calendar"></div>
        <div class="booking-list">
            <h2>Booking Requests</h2>
            <div id="booking-requests">
                <!-- Requests will load dynamically -->
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.6/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script> <!-- Include Axios -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const bookingRequestsEl = document.getElementById('booking-requests');
            const containerEl = document.querySelector('.container');

            // Create summary section
            const summarySection = document.createElement('div');
            summarySection.className = 'summary-section';
            summarySection.innerHTML = `
                <h2>Appointment Summary</h2>
                <div class="summary-tabs">
                    <button class="tab-btn active" data-tab="pending">Pending (0)</button>
                    <button class="tab-btn" data-tab="accepted">Accepted (0)</button>
                    <button class="tab-btn" data-tab="rejected">Rejected (0)</button>
                    <button class="tab-btn" data-tab="rescheduled">Rescheduled (0)</button>
                </div>
                <div id="pending-summary" class="summary-content active">
                    <ul class="summary-list"></ul>
                </div>
                <div id="accepted-summary" class="summary-content">
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
                    fetchAppointments().then(bookings => {
                        const booking = bookings.find(b => b.id === info.event.extendedProps.id);

                        if (booking) {
                            const appointmentDate = new Date(booking.start);
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
                    });
                }
            });
            calendar.render();

            // Tab click event handler
            document.querySelectorAll('.tab-btn').forEach(button => {
                button.addEventListener('click', (e) => {
                    // Remove active class from all buttons and content sections
                    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
                    document.querySelectorAll('.summary-content').forEach(content => content.classList.remove('active'));

                    // Add active class to the clicked button and corresponding content section
                    e.target.classList.add('active');
                    const activeTab = e.target.getAttribute('data-tab');
                    document.getElementById(`${activeTab}-summary`).classList.add('active');
                });
            });

            // Alert modal function
            function showAlert(title, message, type = 'info') {
                Swal.fire({
                    title: title,
                    text: message,
                    icon: type,
                    showConfirmButton: true,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',
                    timer: 3000, // Optional: you can set a timer to auto-close after a few seconds
                });
            }

            // Fetch appointments from the server
            function fetchAppointments() {
                return fetch('../php/get_bookings.php')
                    .then(res => res.json())
                    .catch(err => {
                        console.error('Failed to load bookings:', err);
                        showAlert('Error', 'Failed to load booking data from server.', 'error');
                    });
            }

            // Create summary list item
            function createSummaryListItem(booking) {
                const li = document.createElement('li');
                li.className = `summary-item ${booking.status}`;

                li.innerHTML = `
                <div class="summary-item-header">
                    <span class="booking-name">${booking.title}</span>
                    <span class="booking-date">${new Date(booking.start).toLocaleString()}</span>
                    <span class="status-badge ${booking.status}">${booking.status}</span>
                </div>
            `;

                li.addEventListener('click', () => {
                    showBookingDetails(booking);
                });

                return li;
            }

            // Show booking details with past appointment check
            // function showBookingDetails(booking, calendarEvent = null) {
            //     const showActions = calendarEvent !== null;
            //     const appointmentDate = new Date(booking.start);
            //     const currentDate = new Date();
            //     const isPastAppointment = appointmentDate < currentDate;

            //     Swal.fire({
            //         title: 'Booking Details',
            //         html: `
            //             <p><strong>Title:</strong> ${booking.title}</p>
            //             <p><strong>Email:</strong> ${booking.email}</p>
            //             <p><strong>Date:</strong> ${new Date(booking.start).toLocaleString()}</p>
            //             <p><strong>Description:</strong> ${booking.description}</p>
            //             <p><strong>Status:</strong> ${booking.status}</p>
            //             <p><strong>Request Sent:</strong> ${new Date(booking.created_at).toLocaleString()}</p>
            //             ${isPastAppointment ? `<p class="past-appointment-warning">This appointment has already passed</p>` : ''}
            //         `,
            //         showCancelButton: true,
            //         cancelButtonText: 'Close',
            //         confirmButtonText: showActions && !isPastAppointment && booking.status === 'accepted' ? 'Manage' : 'Close',
            //         preConfirm: () => {
            //             if (showActions && !isPastAppointment && booking.status === 'accepted') {
            //                 showRescheduleModal(booking);
            //             }
            //         },
            //     });
            // }

            function showBookingDetails(booking, calendarEvent = null) {
                const showActions = calendarEvent !== null;
                const appointmentDate = new Date(booking.start);
                const currentDate = new Date();
                const isPastAppointment = appointmentDate < currentDate;

                Swal.fire({
                    title: 'Booking Details',
                    html: `
            <p><strong>Title:</strong> ${booking.title}</p>
            <p><strong>Email:</strong> ${booking.email}</p>
            <p><strong>Date:</strong> ${new Date(booking.start).toLocaleString()}</p>
            <p><strong>Description:</strong> ${booking.description}</p>
            <p><strong>Status:</strong> ${booking.status}</p>
            <p><strong>Request Sent:</strong> ${new Date(booking.created_at).toLocaleString()}</p>
            ${isPastAppointment ? `<p class="past-appointment-warning">This appointment has already passed</p>` : ''}
        `,
                    showCancelButton: true,
                    cancelButtonText: 'Close',
                    confirmButtonText: showActions && !isPastAppointment && booking.status === 'accepted' ? 'Manage' : (booking.status === 'pending' ? 'Accept / Reject' : 'Close'),
                    preConfirm: () => {
                        if (showActions && !isPastAppointment && booking.status === 'accepted') {
                            showRescheduleModal(booking); // Show reschedule modal for accepted
                        } else if (booking.status === 'pending') {
                            showPendingActionsModal(booking); // Show accept/reject modal for pending
                        }
                    },
                });
            }

            function showPendingActionsModal(booking) {
                Swal.fire({
                    title: 'Manage Pending Appointment',
                    html: `
            <p>Do you want to accept or reject this booking?</p>
        `,
                    showCancelButton: true,
                    cancelButtonText: 'Reject',
                    confirmButtonText: 'Accept',
                    showDenyButton: true,
                    denyButtonText: 'Hide Modal', // New "Hide" button
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    denyButtonColor: '#f39c12', // Optional: Customize the deny button color
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Accept the booking
                        updateBooking(booking.id, 'accepted');
                    } else if (result.isDenied) {
                        // Hide modal, no action taken
                        console.log('Modal closed without action');
                    } else {
                        // Reject the booking if "Reject" button was clicked
                        updateBooking(booking.id, 'rejected');
                    }
                });
            }



            // Reschedule Modal using SweetAlert2
            function showRescheduleModal(booking) {
                Swal.fire({
                    title: 'Reschedule Appointment',
                    html: `
                        <p>Choose a new date:</p>
                        <input type="datetime-local" id="reschedule-date" value="${new Date(booking.start).toISOString().slice(0, 16)}" />
                    `,
                    focusConfirm: false,
                    preConfirm: () => {
                        const newDate = document.getElementById('reschedule-date').value;
                        if (!newDate) {
                            Swal.showValidationMessage('Please choose a new date');
                        }
                        return newDate;
                    },
                    showCancelButton: true,
                    cancelButtonText: 'Cancel',
                    confirmButtonText: 'Reschedule',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                }).then((result) => {
                    if (result.isConfirmed) {
                        const newDate = result.value;
                        if (newDate) {
                            updateBooking(booking.id, 'rescheduled', newDate);
                            Swal.fire(
                                'Rescheduled!',
                                'The appointment has been rescheduled.',
                                'success'
                            );
                        }
                    }
                });
            }

            // Update booking status
            function updateBooking(id, status, newStartDate = null) {
                const data = {
                    id,
                    status
                };
                if (newStartDate) {
                    data.start = newStartDate; // Include the new start date for rescheduling
                }

                axios.post('../php/update_booking.php', data)
                    .then(response => {
                        loadBookings(); // Reload bookings after update
                    })
                    .catch(err => {
                        console.error('Error updating booking:', err);
                    });
            }


            // Load bookings from the server
            function loadBookings() {
                fetchAppointments().then(bookings => {
                    bookingRequestsEl.innerHTML = '';
                    document.querySelectorAll('.summary-list').forEach(list => list.innerHTML = '');

                    const statusCounts = {
                        pending: 0,
                        accepted: 0,
                        rejected: 0,
                        rescheduled: 0
                    };

                    calendar.removeAllEvents();

                    bookings.forEach(booking => {
                        const appointmentDate = new Date(booking.start);
                        const isPastAppointment = appointmentDate < new Date();

                        // Increment status count
                        if (statusCounts[booking.status] !== undefined) {
                            statusCounts[booking.status]++;
                        }

                        // Add the booking to the appropriate summary
                        const listItem = createSummaryListItem(booking);
                        const statusSummaryEl = document.getElementById(`${booking.status}-summary`);
                        statusSummaryEl.querySelector('.summary-list').appendChild(listItem);

                        // Add the event to the calendar
                        calendar.addEvent({
                            id: booking.id.toString(),
                            title: booking.title,
                            start: appointmentDate,
                            extendedProps: booking,
                            color: getStatusColor(booking.status),
                            textColor: isPastAppointment ? '#999999' : '#ffffff'
                        });
                    });

                    // Update status counts in the tab buttons
                    Object.keys(statusCounts).forEach(status => {
                        document.querySelector(`[data-tab="${status}"]`).textContent = `${capitalizeFirstLetter(status)} (${statusCounts[status]})`;
                    });
                });
            }

            // Helper to get status color
            function getStatusColor(status) {
                switch (status) {
                    case 'pending':
                        return '#f39c12';
                    case 'accepted':
                        return '#2ecc71';
                    case 'rejected':
                        return '#e74c3c';
                    case 'rescheduled':
                        return '#3498db';
                    default:
                        return '#f39c12';
                }
            }

            // Helper to capitalize the first letter of a string
            function capitalizeFirstLetter(string) {
                return string.charAt(0).toUpperCase() + string.slice(1);
            }

            loadBookings();
        });
    </script>
</body>

</html>