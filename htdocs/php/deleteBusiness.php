<?php
include 'conn.php';
$db = new DatabaseHandler();

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

// Get the POST data (message ID and reason)
$businessId = isset($data['business-id']) ? $data['business-id'] : null;
$reason = isset($data['reason_for_delete']) ? $data['reason_for_delete'] : null;

if ($reason && $businessId) {
    $business = $db->getRowById('businesses', $businessId);  // Fetch row from 'users' table where id = 1
    if ($business) {
        // Prepare the data for the update
        $data = [
            'status' => 'deleted',
            'reason_for_delete' => $reason
        ];

        // Set the condition for the update (business ID)
        $conditions = [
            'id' => $businessId
        ];

        // Call the update method to update the business
        $updateSuccess = $db->update('businesses', $data, $conditions);
        $action = "delete";
        $tableName = "businesses";
        $recordId = null;

        $db->logActionVerbose(
            userId: $_SESSION['user_id'],
            userName: 'Admin',
            action: $action,
            tableName: $tableName,
            recordId: $recordId,
            reason: $reason,
            description: 'business id:' . $businessId
        );
        if ($updateSuccess) {
            echo json_encode(['success' => true, 'message' => 'Business deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error deleting business.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Business not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
