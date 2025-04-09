<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ugnaypinsao";  // Database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set a predefined email address
$email = 'ugnaypinsao@gmail.com'; // Change this to the desired recipient email

// Function to generate a random 6-digit code
function generateRandomCode() {
    return rand(100000, 999999);
}

// Generate the random code
$code = generateRandomCode();
$time = time(); // Get the current time

// Save the code and timestamp to the database
$stmt = $conn->prepare("INSERT INTO two_factor_codes (email, code, created_at) VALUES (?, ?, NOW())");
$stmt->bind_param("ss", $email, $code);
$stmt->execute();

// Close the statement and connection
$stmt->close();
$conn->close();

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
        echo 'sent'; // Indicate that the code was sent successfully
    } else {
        echo 'Email sending failed';
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
