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
$teachers = $userModel->getAllTeachers();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Management</title>
    <link rel="stylesheet" href="../assets/css/admin_dashboard.css">
</head>
<body>
    <div class="container">
        <h1>Teacher Management</h1>
        <div class="teacher-list">
            <?php foreach ($teachers as $teacher): ?>
                <div class="teacher">
                    <?php echo htmlspecialchars($teacher['username']); ?>
                    <a href="update_teacher.php?id=<?php echo $teacher['user_id']; ?>" class="btn">Update</a>
                    <a href="delete_teacher.php?id=<?php echo $teacher['user_id']; ?>" class="btn" onclick="return confirm('Are you sure you want to delete this teacher?');">Delete</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
