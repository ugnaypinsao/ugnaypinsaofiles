<?php 
include 'conn.php';

$db = new DatabaseHandler();

$row = $db->getRow('person_information',['id' => htmlentities($_GET['id'])]);

echo json_encode($row);
