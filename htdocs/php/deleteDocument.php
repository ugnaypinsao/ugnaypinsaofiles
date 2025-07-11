<?php
include 'conn.php';
$db = new DatabaseHandler();

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

// Get the POST data (message ID and reason)
$documentId = isset($data['document-id']) ? $data['document-id'] : null;
$reason = isset($data['reason_for_delete']) ? $data['reason_for_delete'] : null;

if ($documentId && $reason) {
    // Check if document exists (use the getRowById method)
    $document = $db->getRowById('documents', $documentId);

    if ($document) {
        // Prepare data for the update method
        $data = [
            'status' => 'deleted',
            'reason_for_delete' => $reason
        ];

        // Conditions for the update query
        $conditions = [
            'id' => $documentId
        ];

        // Use the update method to update the document status and reason
        $updateSuccess = $db->update('documents', $data, $conditions);
        $action = "delete";
        $tableName = "documents";
        $recordId = null;

        $db->logActionVerbose(
            userId: $_SESSION['user_id'],
            userName: 'Admin',
            action: $action,
            tableName: $tableName,
            recordId: $recordId,
            reason: $reason,
            description: 'document id:' . $documentId
        );
        if ($updateSuccess) {
            echo json_encode(['success' => true, 'message' => 'Document deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating document status.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Document not found.']);
    }
}
