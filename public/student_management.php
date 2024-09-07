<?php

require_once '../config/database.php';

// Include necessary files
require_once '../models/User.php';
include '../includes/dashboard_header.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../views/login.php');
    exit();
}

$userModel = new User();
$students = $userModel->getAllStudents();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management</title>
    <link rel="stylesheet" href="../assets/css/admin_dashboard.css">
</head>
<body>
    <div class="container">
        <h1>Student Management</h1>
        <div class="student-list">
            <?php foreach ($students as $student): ?>
                <div class="student">
                    <?php echo htmlspecialchars($student['username']); ?>
                    <a href="update_student.php?id=<?php echo $student['user_id']; ?>" class="btn">Update</a>
                    <a href="delete_student.php?id=<?php echo $student['user_id']; ?>" class="btn" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
