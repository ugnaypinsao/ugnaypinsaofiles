<?php 
include 'conn.php';

$db = new DatabaseHandler();

// Read JSON body from Axios
$data = json_decode(file_get_contents('php://input'), true);

$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

// Debugging
// var_dump($data);

// Authenticate user (using email here instead of username for clarity)
if ($db->authenticateUser($email, $password)) {
    echo json_encode(['success' => true, 'message' => 'Login successful!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials.']);
}
