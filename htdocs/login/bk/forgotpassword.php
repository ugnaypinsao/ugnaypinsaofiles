<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QCUnnectED Dashboard</title>
    <link rel="stylesheet" href="../assets/css/forgotpassword.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- <script defer src="../assets/js/forgotpassword.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/emailjs-com@3/dist/email.min.js"></script>

</head>

<body>
    <div class="container">
        <div class="logo-container">
            <div class="logo-circle"></div>
            <h1 class="logo">QCUnnectED</h1>
        </div>

        <!-- forgot Password Box -->
        <div class="forgot-password-box" id="forgot-box">
            <div class="icon">!</div>
            <h2>Forgot Password</h2>
            <p>Enter your email below to receive password reset code instructions.</p>
            <form id="forgot-form">
                <label for="email">Email<span class="required">*</span></label>
                <input type="email" id="email" name="email" required>
                <button type="submit">Submit</button>
            </form>
            <a href="logipage1.html" class="back-link">‹ Back to Login</a>
        </div>

        <!-- Reset Code Box (Initially Hidden) -->
        <div class="forgot-password-box" id="reset-box" style="display: none;">
            <div class="icon">!</div>
            <h2>Enter Reset Code</h2>
            <p>
                Please enter the password reset code below that was sent to
                your email
            </p>
            <div class="code-inputs">
                <input type="text" maxlength="1" />
                <input type="text" maxlength="1" />
                <input type="text" maxlength="1" />
                <input type="text" maxlength="1" />
                <input type="text" maxlength="1" />
                <input type="text" maxlength="1" />
            </div>
            <button id="reset-password-btn">Reset Password</button>
            <a href="#" class="back-link" id="back-to-forgot">‹ Back</a>
        </div>

        <!-- New Password Box (Initially Hidden) -->
        <div class="forgot-password-box" id="new-password-box" style="display: none;">
            <div class="icon">!</div>
            <h2>New Password</h2>
            <p>Enter your new password to reset your account.</p>
            <form id="new-password-form">
                <div class="password-wrapper">
                    <input type="password" id="new-password" placeholder="Enter new password" />
                    <span class="toggle-password" onclick="togglePassword('new-password', this)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="eye-icon" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7s-8.268-2.943-9.542-7z" />
                        </svg>
                    </span>
                </div>

                <div class="password-wrapper">
                    <input type="password" id="confirm-password" placeholder="Confirm new password" />
                    <span class="toggle-password" onclick="togglePassword('confirm-password', this)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="eye-icon" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7s-8.268-2.943-9.542-7z" />
                        </svg>
                    </span>
                </div>

                <button type="submit" id="save-password-btn">Save New Password</button>
                <a href="#" class="back-link" id="back-to-reset">‹ Back</a>
            </form>
        </div>
    </div>



    <script>
        document.getElementById('forgot-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('email').value;

            fetch('../server/check_email.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `email=${encodeURIComponent(email)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const userEmail = document.getElementById('email').value.trim(); // ✅ get the user email

                        emailjs.init('bOUnMmgDoM04sRHGx');
                        emailjs.send('service_mioi2fb', 'template_06vwhet', {
                                email: userEmail, // ✅ use user email dynamically
                                otp: data.otp
                            })
                            .then(() => {
                                Swal.fire('OTP sent!', 'Check your email for the OTP.', 'success');
                                document.getElementById('forgot-box').style.display = 'none';
                                document.getElementById('reset-box').style.display = 'block';
                            }, err => {
                                console.error(err);
                                Swal.fire('Oops!', 'Failed to send email.', 'error');
                            });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                });
        });

        document.getElementById('reset-password-btn').addEventListener('click', function() {
            const inputs = document.querySelectorAll('.code-inputs input');
            const otpInput = Array.from(inputs).map(input => input.value).join('');

            fetch('../server/validate_otp.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `otp=${otpInput}&email=${encodeURIComponent(document.getElementById('email').value)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('Success', 'OTP verified.', 'success');
                        document.getElementById('reset-box').style.display = 'none';
                        document.getElementById('new-password-box').style.display = 'block';
                    } else {
                        Swal.fire('Error', 'Invalid OTP.', 'error');
                    }
                });
        });

        document.getElementById('new-password-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const newPass = document.getElementById('new-password').value;
            const confirmPass = document.getElementById('confirm-password').value;

            if (newPass !== confirmPass) {
                Swal.fire('Error', 'Passwords do not match.', 'error');
                return;
            }

            fetch('../server/save_password.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `email=${encodeURIComponent(document.getElementById('email').value)}&password=${encodeURIComponent(newPass)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('Success', 'Password changed successfully.', 'success')
                            .then(() => window.location.href = 'index.php');
                    } else {
                        Swal.fire('Error', 'Failed to change password.', 'error');
                    }
                });
        });
    </script>


</body>

</html>