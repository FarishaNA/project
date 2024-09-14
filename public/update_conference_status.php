<?php
session_start();
require_once '../config/database.php';

$classroomId = $_GET['classroom_id'] ?? '';
$userId = $_SESSION['user_id'] ?? '';

// Check if a conference is ongoing for the classroom
$query = "SELECT is_active FROM video_sessions WHERE classroom_id = $classroomId AND teacher_id = $userId";
$result = mysqli_query($conn, $query);

$response = [];
if ($result) {
    $session = mysqli_fetch_assoc($result);
    $response['is_active'] = $session['is_active'] ?? 0;
} else {
    $response['is_active'] = 0;
}

header('Content-Type: application/json');
echo json_encode($response);
?>
