<?php
include 'conn.php';
$db = new DatabaseHandler();

$documents = $db->getAllRows('documents');
echo json_encode(['success' => true, 'documents' => $documents]);
