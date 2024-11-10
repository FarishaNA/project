<?php
// Include necessary files
require_once '../config/database.php';
require_once '../models/Classroom.php';
require_once '../models/Notification.php'; // Add Notification model
include '../includes/back_button.php';

// Ensure the user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../views/login.php');
    exit();
}

// Create an instance of the Classroom model and Notification model
$classroomModel = new Classroom();
$notificationModel = new Notification();
$studentId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $classroomId = intval($_POST['classroom_id']);

    // Attempt to join the classroom
    $result = $classroomModel->joinClassroom($studentId, $classroomId);

    if ($result === 'joined') {
        // Get the teacher of the classroom
        $teacherId = $classroomModel->getTeacherIdByClassroomId($classroomId);

        // Send a notification to the teacher
        $classroomName = $classroomModel->getClassroomNameById($classroomId);
        $message = "A new student has joined your classroom ".$classroomName;
        $notificationId = $notificationModel->insertNotification($studentId, $message);
         echo $teacherId;
         echo $classroomName ,$message;

        // Wrap the teacher's ID in an array so it can be processed in the foreach loop
        $notificationModel->insertNotificationRecipients($notificationId, [$teacherId], $studentId);

        // Redirect to the student dashboard after successful joining and notification
        header('Location: student_dashboard.php');
        exit();
    } else if ($result === 'already_joined') {
        $error = 'You are already in this classroom.';
    } else if ($result === 'user_not_found') {
        $error = 'User not found. Please try again.';
    } else {
        $error = 'Failed to join the classroom. Please try again later.';
    }
}

// Fetch all available classrooms
$classrooms = $classroomModel->getAllClassrooms();

// Pass data to the view
include '../views/join_classroom.php';
