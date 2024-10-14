<?php
session_start();
require_once '../config/database.php';
require_once '../models/Classroom.php';


if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header('Location: ../views/login.php');
    exit();
}

// Get the classroom ID from the query string
$classroomId = isset($_GET['id']) ? $_GET['id'] : null;

// Create an instance of the Classroom model
$classroomModel = new Classroom();

// Get the user role and user ID
$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'];

if ($classroomId) {
    if ($userRole === 'student') {
        // Student leaves the classroom
        if ($classroomModel->leaveClassroom($userId, $classroomId)) {
            echo "You have successfully left the classroom.";
        } else {
            echo "Failed to leave the classroom.";
        }
    } elseif ($userRole === 'teacher') {
        // Check for the action (delete or update)
        $action = isset($_GET['action']) ? $_GET['action'] : null;
        
        if ($action === 'delete') {
            // Teacher deletes the classroom
            if ($classroomModel->deleteClassroom($classroomId)) {
                echo "Classroom deleted successfully.";
            } else {
                echo "Failed to delete the classroom.";
            }
        } elseif ($action === 'update') {
            // Add your update logic here
            // For example, redirect to an update page or handle the update logic
            header("Location: update_classroom.php?id=$classroomId");
            exit();
        }
    } else {
        echo "Invalid user role.";
    }
} else {
    echo "Invalid classroom ID.";
}
?>
