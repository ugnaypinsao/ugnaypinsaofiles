
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QCUnnectED Dashboard</title>
    <link rel="stylesheet" href="../assets/css/register.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script defer src="../assets/js/register.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

</head>

<body>
    <div class="main-container">
        <div class="left-section">
            <div class="logo-circle"></div>
            <h2>QCUnnectED</h2>
        </div>

        <div class="right-section">
            <form id="signup-form">
                <h3>SIGN - UP</h3>

                <div class="input-group">
                    <span class="icon">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4 4h16v16H4V4zm8 9l8-5H4l8 5zm0 2l-8-5v10h16V10l-8 5z" />
                        </svg>
                    </span>
                    <input type="email" id="email" placeholder="Email" required />
                </div>

                <div class="input-group">
                    <span class="icon">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z" />
                        </svg>
                    </span>
                    <input type="text" id="username" placeholder="Username" required />
                </div>

                <div class="input-group">
                    <span class="icon">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17 8V7a5 5 0 10-10 0h2a3 3 0 116 0v1H7v13h10V8h-2z" />
                        </svg>
                    </span>
                    <input type="password" id="password" placeholder="Password" required />
                    <span class="toggle-eye" onclick="togglePassword('password', this)">
                        <svg class="eye-icon show-eye" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="white" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>

                        <svg class="eye-icon hide-eye" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="white" stroke-width="2" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.07 10.07 0 012.64-4.263m3.29-2.147A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.07 10.07 0 01-4.156 5.338M15 12a3 3 0 00-3-3m0 0a3 3 0 013 3m-3-3v.001M3 3l18 18" />
                        </svg>
                    </span>
                </div>

                <div class="password-constraints">
                    <p id="length" class="invalid">Min. 6 characters</p>
                    <p id="uppercase" class="invalid">At least 1 uppercase</p>
                    <p id="number" class="invalid">At least 1 number</p>
                    <p id="special" class="invalid">At least 1 special character</p>
                </div>

                <div class="input-group">
                    <span class="icon">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17 8V6a5 5 0 00-10 0v2H5v13h14V8h-2zM9 6a3 3 0 116 0v2H9V6z" />
                        </svg>
                    </span>
                    <input type="password" id="confirm-password" placeholder="Verify Password" required />
                    <span class="toggle-eye" onclick="togglePassword('password', this)">
                        <svg class="eye-icon show-eye" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="white" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>

                        <svg class="eye-icon hide-eye" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="white" stroke-width="2" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.07 10.07 0 012.64-4.263m3.29-2.147A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.07 10.07 0 01-4.156 5.338M15 12a3 3 0 00-3-3m0 0a3 3 0 013 3m-3-3v.001M3 3l18 18" />
                        </svg>
                    </span>
                </div>

                    <button type="submit" class="next-btn">NEXT</button>

                <button type="button" class="clear-btn" onclick="clearForm()">Clear Information</button>

                <p class="login-text">Already have an account? <a href="index.php">Click Here</a></p>
            </form>
        </div>
    </div>


</body>
<script>
    document.getElementById('signup-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const email = document.getElementById('email').value.trim();
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();
    const confirmPassword = document.getElementById('confirm-password').value.trim();

    if (password !== confirmPassword) {
        alert('Passwords do not match!');
        return;
    }

    axios.post('../server/register.php', {
            email: email,
            username: username,
            password: password
        })
        .then(function(response) {
            if (response.data.success) {
                alert('Registration successful! Redirecting...');
                window.location.href = "avatar.php";
            } else {
                alert('Error: ' + response.data.message);
            }
        })
        .catch(function(error) {
            console.error('An error occurred:', error);
            alert('An unexpected error occurred. Please try again.');
        });
});

</script>
</html>