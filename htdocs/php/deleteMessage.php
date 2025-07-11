<?php
include 'conn.php';
$db = new DatabaseHandler();

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

// Get the POST data (message ID and reason)
$id = isset($data['id']) ? $data['id'] : null;
$reason = isset($data['reason']) ? $data['reason'] : null;

if ($id && $reason) {
    try {
        // Prepare the data to update
        $data = [
            'status' => 'Deleted',  // Set the status to 'deleted'
            'deletion_reason' => $reason  // Store the reason for deletion
        ];

        // Call the update function to mark the message as deleted
        $deleteSuccess = $db->update('messages', $data, ['id' => $id]);


        $action = "delete";
        $tableName = "messages";
        $recordId = $id;

        $db->logActionVerbose(
            userId: $_SESSION['user_id'],
            userName: 'Admin',
            action: $action,
            tableName: 'messages',
            recordId: $recordId,
            reason: $reason,
            description: null
        );


        if ($deleteSuccess) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update message status']);
        }
    } catch (Exception $e) {
        // Catch any exceptions and return an error
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
}
