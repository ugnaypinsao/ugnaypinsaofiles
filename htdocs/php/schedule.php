<?php
include 'conn.php';

$db = new DatabaseHandler();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

try {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!isset($data['title'], $data['start'], $data['email'], $data['description'])) {
        throw new Exception("Missing required fields.");
    }

    // Convert ISO datetime to PHP DateTime and define window
    $startDateTime = new DateTime($data['start']);
    $windowStart = (clone $startDateTime)->modify('-15 minutes')->format('Y-m-d H:i:s');
    $windowEnd = (clone $startDateTime)->modify('+15 minutes')->format('Y-m-d H:i:s');

    // Check if time slot is already taken
    $conflicts = $db->getRowsWithCustomConditions('appointments', [
        [
            'column' => 'start',
            'operator' => 'BETWEEN',
            'value' => [$windowStart, $windowEnd]
        ]
    ]);

    if (count($conflicts) > 0) {
        throw new Exception("This time slot is already booked. Please choose a different time.");
    }

    // Insert appointment
    $insertData = [
        'title' => $data['title'],
        'start' => (new DateTime($data['start']))->format('Y-m-d H:i:s'),
        'email' => $data['email'],
        'description' => $data['description']
    ];

    $db->insert('appointments', $insertData);

    $action = "add";
    $tableName = "messages";
    $recordId = null;

    $db->logActionVerbose(
        userId: $data['email'],
        userName:$data['email'] . ' appointment',
        action: $action,
        tableName: 'appointments',
        recordId: $recordId,
        reason: $data['title'],
        description: $data['description']
    );

    echo json_encode([
        'status' => 'success',
        'message' => 'Appointment booked successfully.',
        'event' => $insertData
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
