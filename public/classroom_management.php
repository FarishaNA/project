<?php

require_once '../config/database.php';
require_once '../models/Classroom.php';
include '../includes/dashboard_header.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../views/login.php');
    exit();
}

$classroomModel = new Classroom();
$classrooms = $classroomModel->getAllClassrooms();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classroom Management</title>
    <link rel="stylesheet" href="../assets/css/admin_dashboard.css">
</head>
<body>
    <div class="container">
        <h1>Classroom Management</h1>
        <div class="classroom-list">
            <?php foreach ($classrooms as $classroom): ?>
                <div class="classroom">
                    <a href="classroom_details.php?id=<?php echo $classroom['classroom_id']; ?>">
                        <?php echo htmlspecialchars($classroom['classroom_name']); ?>
                    </a>
                    <a href="update_classroom.php?id=<?php echo $classroom['classroom_id']; ?>" class="btn">Update</a>
                    <a href="delete_classroom.php?id=<?php echo $classroom['classroom_id']; ?>" class="btn" onclick="return confirm('Are you sure you want to delete this classroom?');">Delete</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
