<?php
include 'conn.php'; // Make sure this file contains your PDO connection setup

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the JSON input
    $data = json_decode(file_get_contents('php://input'), true);

    // Sanitize input
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $subject = trim($data['subject'] ?? '');
    $message = trim($data['message'] ?? '');

    // Basic validation for required fields
    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format.']);
        exit;
    }

    try {
        // Create a new database handler instance
        $db = new DatabaseHandler();
        $tableName = 'messages'; // Your table name

        // Insert data without returning a hash
        $insertResult = $db->insert($tableName, $data);

        // Check if insertion was successful
        if ($insertResult) {
            $action = "add";
            $tableName = "messages";
            $recordId = null;

            $db->logActionVerbose(
                userId: $email,
                userName: $name.' message',
                action: $action,
                tableName: $tableName,
                recordId: $recordId,
                reason: $subject,
                description: $message
            );

            echo json_encode([
                'status' => 'success',
                'message' => 'Message sent successfully'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to insert data into the database']);
        }
    } catch (Exception $e) {
        // Catch any exceptions (e.g., database issues)
        echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
