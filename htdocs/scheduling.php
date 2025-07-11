<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Appointment | Professional Booking System</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="assets/css/res_sched.css">
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo-placeholder">Pinsao Proper</div>
            <h1>Schedule an Appointment</h1>
            <p class="subtitle">Fill out the form below to book your consultation</p>
        </div>

        <form id="booking-form">
            <div class="form-group">
                <label for="name">Your Name</label>
                <div class="input-container">
                    <input type="text" id="name" name="name" required placeholder="John Doe">
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-container">
                    <input type="email" id="email" name="email" required placeholder="john@example.com">
                </div>
            </div>

            <div class="form-group">
                <label for="details">Appointment Details</label>
                <div class="input-container">
                    <textarea id="details" name="details" rows="4" required placeholder="Please describe the purpose of your appointment"></textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group half-width">
                    <label for="date">Date</label>
                    <div class="input-container">
                        <input type="date" id="date" name="date" required>
                    </div>
                </div>
                <div class="form-group half-width">
                    <label for="time">Time</label>
                    <div class="input-container">
                        <input type="time" id="time" name="time" required>
                    </div>
                </div>
            </div>

            <div class="form-footer">
                <button type="submit" class="submit-button">
                    <span class="button-text">Book Appointment</span>
                    <span class="button-icon">→</span>
                </button>
                <p class="disclaimer">We'll confirm your appointment via email within 24 hours.</p>
            </div>
        </form>
    </div>

    <!-- Success Modal -->
    <div id="popup-modal" class="modal">
        <div class="modal-content">
            <div class="modal-icon">✓</div>
            <h2 class="modal-title">Appointment Booked!</h2>
            <p id="popup-message" class="modal-message"></p>
            <div class="modal-summary">
                <div class="summary-item">
                    <span class="summary-label">Date:</span>
                    <span id="summary-date" class="summary-value"></span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Time:</span>
                    <span id="summary-time" class="summary-value"></span>
                </div>
            </div>
            <button id="ok-button" class="modal-button">
                Done
            </button>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        document.getElementById('booking-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const details = document.getElementById('details').value.trim();
            const date = document.getElementById('date').value;
            const time = document.getElementById('time').value;

            const appointment = {
                title: name + ' - ' + details,
                start: `${date}T${time}`, // ISO format expected by backend
                email: email,
                description: details
            };

            axios.post('php/schedule.php', appointment)
                .then(response => {
                    document.getElementById('popup-message').textContent = "Your appointment has been scheduled successfully.";
                    document.getElementById('summary-date').textContent = date;
                    document.getElementById('summary-time').textContent = time;
                    document.getElementById('popup-modal').style.display = 'block';
                })
                .catch(error => {
                    alert('Failed to schedule appointment. Please choose a different time.');
                    console.error(error);
                });
        });

        document.getElementById('ok-button').addEventListener('click', function() {
            document.getElementById('popup-modal').style.display = 'none';
        });
    </script>


</body>

</html>