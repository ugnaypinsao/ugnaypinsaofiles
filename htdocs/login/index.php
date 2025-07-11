<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login Page</title>
    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="../assets/css/simplegrid.css">
    <link rel="stylesheet" href="../assets/css/icomoon.css">
    <link rel="stylesheet" href="../assets/css/lightcase.css">
    <link rel="stylesheet" href="../js/owl-carousel/owl.carousel.css" />
    <link rel="stylesheet" href="../js/owl-carousel/owl.theme.css" />
    <link rel="stylesheet" href="../js/owl-carousel/owl.transitions.css" />
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <header id="top-header" class="header-home">
        <div class="grid">
            <div class="col-1-1">
                <div class="content">
                    <div class="logo-wrap">
                        <img src="../assets/images/logo.png" alt="ugnayPinsaoLogo">
                    </div>
                    <nav class="navigation">
                        <input type="checkbox" id="nav-button">
                        <label for="nav-button" onclick></label>
                        <ul class="nav-container">
                            <li><a href="../#home" class="current">Home</a></li>
                            <li><a href="../#services">Services</a></li>
                            <li><a href="../#officials">Officials</a></li>
                            <li><a href="../#contact">Contact</a></li>
                            <li><a href="../html/viewers-announcement.html">Announcements</a></li>
                            <li><a href="#">Log in</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <div class="wrapper">
        <div class="form-inner">
            <form id="login" class="login">
                <div class="field">
                    <input type="email" placeholder="Email Address" id="email" required>
                </div>
                <div class="field">
                    <input type="password" placeholder="Password" id="password" required>
                </div>

                <div class="field btn">
                    <div class="btn-layer"></div>
                    <input type="submit" value="Login" id="submit">
                </div>
            </form>
        </div>
    </div>

    <!-- Axios CDN -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        document.getElementById('login').addEventListener('submit', function(e) {
            e.preventDefault();

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            axios.post('../php/login.php', {
                    email: email,
                    password: password
                })
                .then(function(response) {
                    console.log(response.data);
                    if (response.data.success) {
                        alert(response.data.message);
                        window.location.href = '2fa.php';
                    } else {
                        alert(response.data.message);
                    }
                })
                .catch(function(error) {
                    console.error(error);
                    alert('Login failed. Please check your credentials.');
                });
        });
    </script>
</body>

</html>