<?php
// public/login.php

require_once '../config/database.php'; // Include the database connection
require_once '../models/User.php'; // Include the User model

session_start();

session_unset();

session_destroy();

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = $_POST['email'];
    $password = $_POST['password'];

    $userModel = new User();

    // Check if the user exists by username or email
        $user = $userModel->getUserByEmail($usernameOrEmail);

    // Verify the password
    if ($user &&$password== $user['password']) {
        // Set session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['profile_pic_path']=$user['profile_pic_path'];

        if(isset($_SESSION['username'])){
            echo "hello".$_SESSION['username'];
        }
        // Redirect based on the user role
        switch ($user['role']) {
            case 'admin':
                header('Location: admin_dashboard.php');
                break;
            case 'teacher':
                header('Location: teacher_dashboard.php');
                break;
            case 'student':
                header('Location: student_dashboard.php');
                break;
            default:
                // If role is not recognized, redirect to login page
                header('Location: ../views/login.php');
        }
        exit();
    } else {
        // Invalid login
        $_SESSION['login_error'] = 'Invalid username, email, or password.';
        header('Location: ../views/login.php');
        exit();
    }
} else {
    // If the request method is not POST, redirect to the login form
    header('Location: ../views/login.php');
    exit();
}
?>
