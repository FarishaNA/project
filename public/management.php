<?php

require_once '../config/database.php';
require_once '../models/User.php';
require_once '../models/Classroom.php';
include '../includes/dashboard_header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../views/login.php');
    exit();
}

if(isset($_GET['type']))
    $type = $_GET['type'];
else{
    echo "error";
    exit();
}

if ($type === 'students') {
    $userModel = new User();
    $items = $userModel->getAllStudents();
    $title = "Student Management";
} elseif ($type === 'teachers') {
    $userModel = new User();
    $items = $userModel->getAllTeachers();
    $title = "Teacher Management";
} elseif ($type === 'classrooms') {
    $classroomModel = new Classroom();
    $items = $classroomModel->getAllClassrooms();
    $title = "Classroom Management";
} else {
    echo "Invalid management type.";
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <link rel="stylesheet" href="../assets/css/management.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($title); ?></h1>
        <div class="classroom-list">
            <?php foreach ($items as $item): ?>
                <div class="classroom">
                <?php 
                    // Display the classroom name or username depending on the type
                    if ($type === 'classrooms') {
                        echo htmlspecialchars($item['classroom_name']);
                        $id = $item['classroom_id'];  // Use classroom_id for classrooms
                    } else {
                        echo htmlspecialchars($item['username']);
                        $id = $item['user_id'];  // Use user_id for students and teachers
                    }
                    ?>
                    <div class="icons">
                    <a href="manage_action.php?action=up&id=<?php echo $id; ?>&type=<?php echo $type; ?>" class="icon-btn" id="pencil">
                        <i class="fa fa-pencil"></i>
                    </a>
                    <a href="manage_action.php?action=dlt&id=<?php echo $id; ?>&type=<?php echo $type; ?>" class="icon-btn" id="dlt" onclick="return confirm('Are you sure you want to delete this?');">
                        <i class="fa fa-trash"></i>
                    </a>
                </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
