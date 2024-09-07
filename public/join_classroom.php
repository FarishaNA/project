<?php
// Include necessary files
require_once '../config/database.php';
require_once '../models/Classroom.php';
include '../includes/dashboard_header.php'; 



// Ensure the user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../views/login.php');
    exit();
}

// Create an instance of the Classroom model
$classroomModel = new Classroom();
$studentId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $classroomId = intval($_POST['classroom_id']);

    // Attempt to join the classroom
    $success = $classroomModel->joinClassroom($studentId, $classroomId);

    if ($success) {
        header('Location: student_dashboard.php');
        exit();
    } else {
        $error = 'Failed to join classroom. Please try again.';
    }
}

// Fetch all available classrooms
$classrooms = $classroomModel->getAllClassrooms();

// Pass data to the view
include '../views/join_classroom.php';
?>
