<?php
include 'conn.php';
$db = new DatabaseHandler();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit;
}

$id = (int)$_GET['id'];
try {
    $business = $db->getRowById('businesses', $id);  // Fetch row from 'users' table where id = 1


    if ($business) {
        echo json_encode(['success' => true, 'business' => $business]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Business not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
