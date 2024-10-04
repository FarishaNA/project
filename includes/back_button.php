<?php
session_start();

// Function to redirect based on user role if classroom is not selected
function getRedirectUrl($role) {
    switch ($role) {
        case 'admin':
            return '../public/admin_dashboard.php';
        case 'teacher':
            return '../public/teacher_dashboard.php';
        case 'student':
            return '../public/student_dashboard.php';
        default:
            return '../views/index.php'; // Default page if role is not recognized
    }
}

// Check if the classroom is selected in the session
if (isset($_SESSION['selected_classroom'])) {
    $redirectUrl = "classroom.php?id=" . $_SESSION['selected_classroom'];
} else {
    // If no classroom is selected, redirect based on user role
    $role = $_SESSION['role']; 
    $redirectUrl = getRedirectUrl($role);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Back</title>
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
        .back-icon i {
            font-size: 20px;
            color: #8c5e4f;
        }
    </style>
</head>
<body>

<!-- Back Button Icon -->
<div class="back-container">
    <a href="<?php echo $redirectUrl; ?>" class="back-icon">
        <i class="fas fa-arrow-left"></i> 
    </a>
</div>

</body>
</html>
