<?php
require 'conn.php';
header('Content-Type: application/json');

try {
    $db = new DatabaseHandler();
    $results = $db->getRowsWithCustomConditions('messages', [
        ['column' => 'status', 'operator' => '=', 'value' => 'deleted']
    ]);
    echo json_encode(['success' => true, 'data' => $results]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
