<?php
include '../php/conn.php';
$db = new DatabaseHandler();

$result = $db->getAllRows('logs');

// Capitalize table names properly
foreach ($result as &$row) {
    if ($row['table_name'] == 'person_information') {
        $row['table_name'] = ('Barangay Data');
    }
    $row['table_name'] = ucwords($row['table_name']);
    $row['action'] = ucwords($row['action']);

}
unset($row); // optional but safe practice

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'data' => $result
]);
