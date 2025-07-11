<?php
include '../php/conn.php';
$db = new DatabaseHandler();

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $announcements = $db->getAllRows('announcements');
        echo json_encode($announcements);
        break;

    case 'POST':
        $action = $_POST['action'] ?? null;

        switch ($action) {
            case 'create': // Create a new announcement
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = '../uploads/business_images/';

                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    // Sanitize and generate a unique filename
                    $filename = basename($_FILES['image']['name']);
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    $safeName = uniqid('img_', true) . '.' . $ext;
                    $imagePath = $uploadDir . $safeName;

                    // Move the uploaded file
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                        $relativePath = '../uploads/business_images/' . $safeName;

                        try {
                            $db->insert('announcements', [
                                'title'   => $_POST['title'],
                                'what'    => $_POST['what'],
                                'where_'  => $_POST['where'],
                                'when_'   => $_POST['when'],
                                'content' => $_POST['content'],
                                'image_path' => $relativePath
                            ]);

                            $action = "add";
                            $tableName = "announcements";
                            $recordId = null;

                            $db->logActionVerbose(
                                userId: $_SESSION['user_id'],
                                userName: 'Admin',
                                action: $action,
                                tableName: $tableName,
                                recordId: $recordId,
                                reason: null,
                                description: 'announcement ' . $_POST['title']
                            );

                            echo json_encode(['status' => 'success']);
                        } catch (Exception $e) {
                            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                        }
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file.']);
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'No image uploaded or upload error occurred.']);
                }
                break;

            case 'edit': // Edit an existing announcement
                if (!isset($_POST['id']) || !isset($_POST['title']) || !isset($_POST['what']) || !isset($_POST['where']) || !isset($_POST['when']) || !isset($_POST['content'])) {
                    echo json_encode(['status' => 'error', 'message' => 'Missing required fields.']);
                    break;
                }

                // Handle image upload if a new image is provided
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $imagePath = handleFileUpload($_FILES['image']);
                    if ($imagePath === false) {
                        echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file.']);
                        break;
                    }
                }

                try {
                    if ($imagePath) {
                        $db->update('announcements', [
                            'title'    => $_POST['title'],
                            'what'     => $_POST['what'],
                            'where_'   => $_POST['where'],
                            'when_'    => $_POST['when'],
                            'content'  => $_POST['content'],
                            'image_path' => $imagePath
                        ], [
                            'id' => $_POST['id']
                        ]);
                    } else {
                        $db->update('announcements', [
                            'title'    => $_POST['title'],
                            'what'     => $_POST['what'],
                            'where_'   => $_POST['where'],
                            'when_'    => $_POST['when'],
                            'content'  => $_POST['content'],
                        ], [
                            'id' => $_POST['id']
                        ]);
                    }

                    $action = "update";
                    $tableName = "announcements";
                    $recordId = null;

                    $db->logActionVerbose(
                        userId: $_SESSION['user_id'],
                        userName: 'Admin',
                        action: $action,
                        tableName: $tableName,
                        recordId: $recordId,
                        reason: null,
                        description: 'announcement ' . $_POST['title']
                    );
                    echo json_encode(['status' => 'updated']);
                } catch (Exception $e) {
                    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                }
                break;

            case 'delete': // Delete an announcement
                if (!isset($_POST['id'])) {
                    echo json_encode(['status' => 'error', 'message' => 'Missing announcement ID']);
                    break;
                }

                try {
                    $data = [
                        'status' => 'deleted',
                    ];

                    // Set the condition for the update (business ID)
                    $conditions = [
                        'id' => $_POST['id']
                    ];

                    // Call the update method to update the business
                    $updateSuccess = $db->update('announcements', $data, $conditions);
                    $action = "delete";
                    $tableName = "announcements";
                    $recordId = null;

                    $db->logActionVerbose(
                        userId: $_SESSION['user_id'],
                        userName: 'Admin',
                        action: $action,
                        tableName: $tableName,
                        recordId: $recordId,
                        reason: null,
                        description: 'announcement ' . $_POST['title']
                    );
                    echo json_encode(['status' => 'deleted']);
                } catch (Exception $e) {
                    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                }
                break;

            default:
                echo json_encode(['status' => 'error', 'message' => 'Unknown action']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
}

function handleFileUpload($file)
{
    $uploadDir = '../uploads/business_images/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $filename = basename($file['name']);
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $safeName = uniqid('img_', true) . '.' . $ext;
    $imagePath = $uploadDir . $safeName;

    if (move_uploaded_file($file['tmp_name'], $imagePath)) {
        return '../uploads/business_images/' . $safeName;
    }

    return false;
}
