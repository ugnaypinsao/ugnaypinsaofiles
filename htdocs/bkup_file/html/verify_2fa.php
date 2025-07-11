<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve email and entered code from POST request
    $email = $_POST['email'];
    $enteredCode = $_POST['code'];

    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "ugnaypinsao";  // Database name

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve the code and timestamp from the database
    $stmt = $conn->prepare("SELECT code, created_at FROM two_factor_codes WHERE email = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($correctCode, $createdAt);
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    if (!$correctCode) {
        echo 'expired'; // No code found for the email
        exit;
    }

    // Check if the code matches and if it hasn't expired (5 minutes)
    $timeElapsed = time() - strtotime($createdAt);
    if ($enteredCode == $correctCode && $timeElapsed <= 300) {
        echo 'success'; // Code matches and is within the valid time window
    } else {
        echo 'invalid'; // Code doesn't match or has expired
    }
} else {
    echo 'Invalid request method';
}
?>
