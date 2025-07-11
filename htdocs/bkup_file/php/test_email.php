<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // Enable verbose debug output
    $mail->SMTPDebug = 2; 
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'ugnaypinsao@gmail.com'; // Your Gmail address
    $mail->Password = 'ttcqawixriiwicrd'; // Your Gmail app password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('ugnaypinsao@gmail.com', 'Test Mailer');
    $mail->addAddress('ugnaypinsao@gmail.com'); // Replace with a valid recipient email
    $mail->isHTML(true);
    $mail->Subject = 'Test Email';
    $mail->Body = 'This is a test email.';

    // Attempt to send the email
    if ($mail->send()) {
        echo 'Email sent successfully';
    } else {
        echo 'Email sending failed';
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();  // Log errors if they occur
}
?>
