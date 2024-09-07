<?php

require_once '../config/database.php';
require_once '../models/Classroom.php';
include '../includes/dashboard_header.php';

// Ensure the user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: ../views/login.php');
    exit();
}

// Create an instance of the Classroom model
$classroomModel = new Classroom();

// Fetch all classrooms for the logged-in teacher
$teacherId = $_SESSION['user_id'];
$classrooms = $classroomModel->getAllClassroomsForTeacher($teacherId);

// Check if the user is logging in for the first time in this session
$firstLogin = !isset($_SESSION['first_login']);
if ($firstLogin) {
    $_SESSION['first_login'] = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="../assets/css/teacher_dashboard.css">
    <link rel="stylesheet" href="../assets/css/dashboard_header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <div class="container">
        <h1>Your Classrooms</h1>
        <div class="classroom-list">
            <?php foreach ($classrooms as $classroom): ?>
                <div class="classroom">
                    <div class="options">
                        <span class="dot">•••</span>
                        <div class="dropdown">
                            <a href="../public/update_classroom.php?id=<?php echo $classroom['classroom_id']; ?>">Update</a>
                            <a href="../public/delete_classroom.php?id=<?php echo $classroom['classroom_id']; ?>" onclick="return confirm('Are you sure you want to delete this classroom?');">Delete</a>
                        </div>
                    </div>
                    <a href="../public/classroom.php?id=<?php echo $classroom['classroom_id']; ?>">
                        <?php echo htmlspecialchars($classroom['classroom_name']); ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <button class="create-classroom" onclick="window.location.href='../public/create_classroom.php';"></button>
        <div class="create-classroom-tooltip">Create New Classroom</div>
    </div>
</body>
</html>
