<?php
include '../php/conn.php';
$db = new DatabaseHandler();

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

// Ensure data exists
if (isset($data['id']) && isset($data['status'])) {
    $id = $data['id'];
    $status = $data['status'];
    $newStartDate = isset($data['start']) ? $data['start'] : null; // Check if new date is provided

    // Prepare data to be updated
    $updateData = [
        'status' => $status
    ];

    // If the status is 'rescheduled', also update the start date
    if ($status === 'rescheduled' && $newStartDate) {
        $updateData['start'] = $newStartDate;  // Set the new start date
    }

    // Prepare conditions for the update (we want to update the row where id matches)
    $conditions = [
        'id' => $id
    ];

    // Perform the update using the update method
    $result = $db->update('appointments', $updateData, $conditions);

    // Return a JSON response based on the result
    if ($result) {
        // Success response
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Booking status updated successfully']);
    } else {
        // Failure response
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Failed to update booking status']);
    }
} else {
    // If required data is not set, return an error response
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
}
?>
