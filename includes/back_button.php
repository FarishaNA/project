<?php
session_start();

// Ensure the classroom is selected in the session
if (!isset($_SESSION['selected_classroom'])) {
    echo "Classroom not selected!";
    exit();
}

$classroomId = $_SESSION['selected_classroom'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Back to Classroom</title>
    <!-- External FontAwesome link for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .back-container {
            position: fixed;
            top: 20px;
            left: 20px;
        }
        .back-icon {
            text-decoration: none;
        }
        .back-icon i{
            font-size: 20px;
            color:#8c5e4f;
        }
    </style>
</head>
<body>

<!-- Back Button Icon -->
<div class="back-container">
    <a href="classroom.php?id=<?php echo $classroomId; ?>" class="back-icon">
        <i class="fas fa-arrow-left"></i> <!-- FontAwesome back arrow icon -->
    </a>
</div>

</body>
</html>
