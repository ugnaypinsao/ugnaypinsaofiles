<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Manually include PHPMailer classes
require '../phpmailer/Exception.php';
require '../phpmailer/PHPMailer.php';
require '../phpmailer/SMTP.php';

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ugnaypinsao";  // Database name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Predefined email address
$email = 'ugnaypinsao@gmail.com';

// Generate a random 6-digit code
function generateRandomCode() {
    return rand(100000, 999999);
}

$code = generateRandomCode();

// Store the code in the database
$stmt = $conn->prepare("INSERT INTO two_factor_codes (email, code, created_at) VALUES (?, ?, NOW())");
$stmt->bind_param("ss", $email, $code);
$stmt->execute();
$stmt->close();
$conn->close();

// PHPMailer setup (manual)
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'ugnaypinsao@gmail.com';
    $mail->Password = 'ttcqawixriiwicrd'; // Gmail App Password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('ugnaypinsao@gmail.com', 'Test Mailer');
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = 'Your Verification Code';
    $mail->Body = "Your verification code is <b>$code</b>. It expires in 5 minutes.";

    if ($mail->send()) {
        echo 'sent';
    } else {
        echo 'Email sending failed';
    }
} catch (Exception $e) {
    echo 'Error: ' . $mail->ErrorInfo;
}
?>