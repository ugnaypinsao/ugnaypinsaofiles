<?php
include 'conn.php';
$db = new DatabaseHandler();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['docFile'])) {
    $docTitle = $_POST['docTitle'];
    $docDescription = $_POST['docDescription'];
    $docFile = $_FILES['docFile'];

    // Validate file type (PDF, DOC, DOCX)
    $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    if (!in_array($docFile['type'], $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only PDF and DOC files are allowed.']);
        exit;
    }

    // Generate a unique file name
    $fileName = uniqid() . "_" . basename($docFile['name']);
    $uploadDir = '../uploads/';
    $uploadFile = $uploadDir . $fileName;

    // Move the uploaded file to the desired directory
    if (move_uploaded_file($docFile['tmp_name'], $uploadFile)) {
        // Prepare data for insertion
        $data = [
            'title' => $docTitle,
            'description' => $docDescription,
            'file_url' => $uploadFile
        ];

        // Use the insert method to insert the document data into the database
        try {
            $db->insert('documents', $data);
            $action = "add";
            $tableName = "documents";
            $recordId = null;

            $db->logActionVerbose(
                userId: $_SESSION['user_id'],
                userName: 'Admin',
                action: $action,
                tableName: $tableName,
                recordId: $recordId,
                reason: null,
                description: 'document ' . $docTitle
            );
            echo json_encode(['status' => 'deleted']);
            echo json_encode(['success' => true, 'message' => 'Document uploaded successfully.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error uploading document: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error uploading file.']);
    }
}
