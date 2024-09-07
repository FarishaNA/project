<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../public/login.php');
    exit;
}

// Redirect based on user role
function redirectBasedOnRole() {
    if ($_SESSION['user_role'] === 'admin') {
        header('Location: ../views/admin_dashboard.php');
    } elseif ($_SESSION['user_role'] === 'teacher') {
        header('Location: ../views/teacher_dashboard.php');
    } else {
        header('Location: ../views/student_dashboard.php');
    }
    exit;
}

// Call this function at the top of each dashboard page
redirectBasedOnRole();
