<?php 
include 'conn.php';

$db = new DatabaseHandler();
$newUserId = $db->insertUser('admin', 'admin@gmail.com', 'pass123', 'admin');

if ($newUserId) {
    echo "User created successfully with ID: $newUserId";
} else {
    echo "Failed to create user.";
}
