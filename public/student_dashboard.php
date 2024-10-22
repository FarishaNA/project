<?php
require_once '../config/database.php';
require_once '../models/Classroom.php';
include '../includes/dashboard_header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../views/login.php');
    exit();
}

$classroomModel = new Classroom();
$studentId = $_SESSION['user_id'];
$classrooms = $classroomModel->getClassroomsForStudent($studentId);

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
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../assets/css/student_dashboard.css">
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
                            <a href="../public/manage_classroom.php?id=<?php echo $classroom['classroom_id']; ?>&action=leave" onclick="return confirm('Are you sure you want to leave this classroom?');">Leave Classroom</a>
                        </div>
                    </div>
                    <a href="../public/classroom.php?id=<?php echo $classroom['classroom_id']; ?>">
                        <?php echo htmlspecialchars($classroom['classroom_name']); ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <button class="join-classroom" onclick="window.location.href='../public/join_classroom.php';"></button>
        <div class="join-classroom-tooltip">Join a Classroom</div>
    </div>
</body>
</html>
