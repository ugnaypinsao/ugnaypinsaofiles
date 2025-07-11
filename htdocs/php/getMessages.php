<?php
include 'conn.php';

header('Content-Type: application/json');

// Check if the method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $db = new DatabaseHandler();
        $tableName = 'messages'; // Table containing the messages

        $messages = $db->getRowsWithCustomConditions($tableName, [
            ['column' => 'status', 'operator' => '!=', 'value' => 'deleted']
        ]);

        echo json_encode(["status" => "success", "messages" => $messages]);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "An error occurred: " . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
