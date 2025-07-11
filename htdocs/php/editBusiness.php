<?php
include '../php/conn.php'; // Include the database connection
$db = new DatabaseHandler();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the necessary data is present in the request
    $businessId = $_POST['business-id'] ?? null;
    $ownerName = $_POST['ownerName'] ?? null;
    $name = $_POST['name'] ?? null;
    $description = $_POST['description'] ?? null;
    $category = $_POST['category'] ?? null;
    $address = $_POST['address'] ?? null;
    $phone = $_POST['phone'] ?? null;
    $hours = $_POST['hours'] ?? null;

    if (!$businessId || !$ownerName || !$name || !$category || !$address) {
        echo json_encode(['success' => false, 'message' => 'Required fields are missing.']);
        exit;
    }

    // Prepare the data array
    $data = [
        'owner_name' => $ownerName,
        'business_name' => $name,
        'description' => $description,
        'category' => $category,
        'address' => $address,
        'phone' => $phone,
        'hours' => $hours,
    ];

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // Check if the file is an image
        $imageTmp = $_FILES['image']['tmp_name'];
        $imageName = $_FILES['image']['name'];
        $imageType = $_FILES['image']['type'];
        $imageSize = $_FILES['image']['size'];

        // Validate image size and type (for example, max 2MB and must be a PNG/JPG)
        if ($imageSize > 2097152 || !in_array($imageType, ['image/jpeg', 'image/png'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid image file.']);
            exit;
        }

        // Move the uploaded image to a folder
        $uploadDir = '../uploads/business_images/';
        $imagePath = $uploadDir . basename($imageName);
        if (move_uploaded_file($imageTmp, $imagePath)) {
            $data['image'] = $imagePath;
        } else {
            echo json_encode(['success' => false, 'message' => 'Error uploading image.']);
            exit;
        }
    }

    // Define the conditions for the update (by business id)
    $conditions = [
        'id' => $businessId,
    ];

    // Perform the update
    $updateSuccess = $db->update('businesses', $data, $conditions);
    $action = "update";
    $tableName = "businesses";
    $recordId = null;

    $db->logActionVerbose(
        userId: $_SESSION['user_id'],
        userName: 'Admin',
        action: $action,
        tableName: $tableName,
        recordId: $recordId,
        reason: null,
        description: 'business ' . $name
    );
    // Respond with a success or failure message
    if ($updateSuccess) {
        echo json_encode(['success' => true, 'message' => 'Business updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating business.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
