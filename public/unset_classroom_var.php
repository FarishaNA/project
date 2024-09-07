<?php
session_start(); // Start the session to access $_SESSION variables

// Unset the selected classroom if it's set
if (isset($_SESSION['selected_classroom'])) {
    unset($_SESSION['selected_classroom']);
}

// Redirect to the appropriate dashboard based on the user role
if ($_SESSION['role'] === 'admin') {
    header('Location: ../public/admin_dashboard.php');
} elseif ($_SESSION['role'] === 'teacher') {
    header('Location: ../public/teacher_dashboard.php');
} elseif ($_SESSION['role'] === 'student') {
    header('Location: ../public/student_dashboard.php');
} else {
    // Handle case where user role is not set or recognized
    header('Location: ../public/login.php');
}

exit();
?>
