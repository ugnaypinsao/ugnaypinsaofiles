<?php
include 'conn.php';
$db = new DatabaseHandler();

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email'], $data['timestamp'], $data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$email = $data['email'];
$timestamp = $data['timestamp'];
$status = $data['status'];

// Call the new method from DatabaseHandler
$success = $db->updateMessageStatus($email, $timestamp, $status);

$db->logActionVerbose(
    userId: $_SESSION['user_id'],
    userName: 'Admin',
    action: 'update',
    tableName: 'appointments',
    recordId: null,
    reason: null,
    description: $email.' appointment to '.$status.'.'
);

echo json_encode(['success' => $success]);
