<?php
session_start();
include '../php/conn.php';

$db = new DatabaseHandler();

// If your `logOut()` method clears session, good! But let's be sure:
$db->logoutUser();

header('Location: ../');
exit;
?>
