<?php
header('Content-Type: application/json');
include 'conn.php';

$db = new DatabaseHandler();

$businesses = $db->getAllRows('businesses');

if ($businesses !== false) {
    echo json_encode([
        'success' => true,
        'businesses' => $businesses
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch business data.'
    ]);
}
?>
