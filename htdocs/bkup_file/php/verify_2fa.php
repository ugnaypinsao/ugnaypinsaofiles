<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve email and entered code from POST request
    $email = $_POST['email'];
    $enteredCode = $_POST['code'];
    $file = "2fa-codes/$email.json";

    // Check if the file exists
    if (!file_exists($file)) {
        echo 'expired';
        exit;
    }

    // Get the stored data from the JSON file
    $data = json_decode(file_get_contents($file), true);
    $correctCode = $data['code'];
    $time = $data['time'];

    // Debugging: Output the entered code and stored code
    error_log("Entered Code: $enteredCode, Correct Code: $correctCode, Timestamp: $time");

    // Trim any spaces from both codes to ensure comparison is done correctly
    if (trim($enteredCode) == trim($correctCode) && (time() - $time) <= 300) {
        unlink($file); // Remove the code file after successful validation
        echo 'success'; // Send a success response
        exit;
    } else {
        echo 'invalid'; // Invalid code or expired
    }
} else {
    echo 'Invalid request method';
}
?>
