<?php
require_once '../config/database.php';
include '../includes/dashboard_header.php';

// Ensure user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../views/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admin_dashboard.css">
</head>
<body>
    

    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <div class="dashboard-links">
        <a href="management.php?type=classrooms" class="dashboard-link">View All Classrooms</a>
        <a href="management.php?type=students" class="dashboard-link">View All Students</a>
        <a href="management.php?type=teachers" class="dashboard-link">View All Teachers</a>
        <a href="create_classroom.php" class="dashboard-link">Add New Classroom</a>
        <a href="create_user.php?role=student" class="dashboard-link">Add New Student</a>
        <a href="create_user.php?role=teacher" class="dashboard-link">Add New Teacher</a>
        </div>
    </div>
</body>
</html>
