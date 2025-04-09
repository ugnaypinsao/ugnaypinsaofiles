<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

// Set a predefined email address
$email = 'ugnaypinsao@gmail.com'; // Change this to the desired recipient email

// Function to generate a random 6-digit code
function generateRandomCode() {
    return rand(100000, 999999);
}

// Generate the random code
$code = generateRandomCode();
$time = time(); // Get the current time

// Save the code and time in a JSON file
file_put_contents("2fa-codes/$email.json", json_encode(['code' => $code, 'time' => $time]));

// PHPMailer setup
$mail = new PHPMailer(true);
try {
    // Enable verbose debug output
    $mail->SMTPDebug = 0; 
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'ugnaypinsao@gmail.com'; // Your Gmail address
    $mail->Password = 'ttcqawixriiwicrd'; // Your Gmail app password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('ugnaypinsao@gmail.com', 'Test Mailer');
    $mail->addAddress($email); // Send to the predefined email
    $mail->isHTML(true);
    $mail->Subject = 'Your Verification Code';
    $mail->Body = "Your verification code is <b>$code</b>. It expires in 5 minutes.";

    // Attempt to send the email
    if ($mail->send()) {
        echo 'sent';
    } else {
        echo 'Email sending failed';
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
