<?php
// config/database.php

$host = 'localhost';
$db = 'virtual_study_space'; // Replace with your database name
$user = 'root'; // Replace with your database username
$pass = ''; // Replace with your database password


// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
