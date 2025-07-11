<?php
include '../php/conn.php';
$db = new DatabaseHandler();

// Adjust table/column names based on your DB schema
$result = $db->getAllRows('appointments');

header('Content-Type: application/json');
echo json_encode($result);
?>
