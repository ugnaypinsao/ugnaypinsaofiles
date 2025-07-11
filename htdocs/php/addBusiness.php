<?php
include 'conn.php';
$db = new DatabaseHandler();

header('Content-Type: application/json');

try {
    // Use $_POST instead of json_decode() when enctype is multipart/form-data
    $ownerName = $_POST['ownerName'];
    $businessName = $_POST['name'];
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'];
    $address = $_POST['address'];
    $phone = $_POST['phone'] ?? '';
    $hours = $_POST['hours'] ?? '';

    // Handle image upload
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['image']['type'], $allowedTypes)) {
            throw new Exception('Invalid image format. Only JPG, PNG, and GIF are allowed.');
        }

        $uploadDir = '../uploads/business_images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imagePath = $uploadDir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    }

    // Prepare data
    $data = [
        'owner_name' => $ownerName,
        'business_name' => $businessName,
        'description' => $description,
        'category' => $category,
        'address' => $address,
        'phone' => $phone,
        'hours' => $hours,
        'image' => $imagePath
    ];

    // Insert
    $insertSuccess = $db->insert('businesses', $data);
    $action = "add";
    $tableName = "businesses";
    $recordId = null;

    $db->logActionVerbose(
        userId: $_SESSION['user_id'],
        userName: 'Admin',
        action: $action,
        tableName: $tableName,
        recordId: $recordId,
        reason: null,
        description: 'business ' . $businessName
    );
    echo json_encode([
        'success' => $insertSuccess,
        'message' => $insertSuccess ? 'Business added successfully.' : 'Failed to add business.'
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
