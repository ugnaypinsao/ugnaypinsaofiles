<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

session_start();

// Configuration
$email = $_SESSION['email'];

// Generate a 6-digit random code
function generateRandomCode()
{
    return rand(100000, 999999);
}

$code = generateRandomCode();
$timestamp = time(); // Store generation time

// Save code and time in session
$_SESSION['2fa_code'] = $code;
$_SESSION['2fa_time'] = $timestamp;

// PHPMailer setup
$mail = new PHPMailer(true);
try {
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'ugnaypinsao@gmail.com'; // Your Gmail address
    $mail->Password = 'ttcqawixriiwicrd';       // Gmail App Password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('ugnaypinsao@gmail.com', 'Ugnay Pinsao');
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = 'Your Verification Code';
    $mail->Body = "Your verification code is <b>$code</b>. It will expire in 5 minutes.";

    $mail->send();
    echo 'sent';
} catch (Exception $e) {
    echo 'Error: ' . $mail->ErrorInfo;
}
