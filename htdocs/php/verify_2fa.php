<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $enteredCode = trim($_POST['code']);
    // Fetch stored code and time from session
    $storedCode = isset($_SESSION['2fa_code']) ? $_SESSION['2fa_code'] : null;
    $storedTime = isset($_SESSION['2fa_time']) ? $_SESSION['2fa_time'] : 0;

    $currentTime = time();  // Get the current timestamp
    $timeDiff = $currentTime - $storedTime;  // Calculate the time difference

    // Ensure both codes are compared as integers
    if ((int)$enteredCode === (int)$storedCode && $timeDiff <= 300) {
        // Clear code and time after successful verification
        unset($_SESSION['2fa_code']);
        unset($_SESSION['2fa_time']);
        $_SESSION['verified']=true;
        echo 'success';
    } else {
        echo 'invalid'; // Either wrong code or expired
    }
} else {
    echo 'Invalid request method';
}
?>
