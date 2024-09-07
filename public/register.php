<?php
require_once '../config/database.php';
require_once '../models/User.php';

session_start(); // Start the session to store error messages

$error = ''; // Variable to store error messages

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    // Basic validation
    if (!$username || !$email || !$password || !$confirm_password) {
        $error = 'All fields are required.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif ($role !== 'student' && $role !== 'teacher') {
        $error = 'Invalid role selected.';
    } else {
        // Create a new User object
        $user = new User();

        // Check if username or email already exists
        if ($user->getUserByUsername($username) || $user->getUserByEmail($email)) {
            $error = 'Username or email already exists.';
        } else {
            // Register the user with plain password
            if ($user->createUser($username, $email, $password, $role)) {
                header('Location: ../views/login.php');
                exit();
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }

    // Store error message in session and redirect back to registration form
    $_SESSION['registration_error'] = $error;
    $_SESSION['form_data'] = $_POST; // Save form data to repopulate fields
    header('Location: ../views/register.php');
    exit();
} else {
    die('Invalid request method.');
}
?>
