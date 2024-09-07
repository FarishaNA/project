<?php

require_once '../config/database.php';
require_once '../models/User.php';
include '../includes/dashboard_header.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../views/login.php');
    exit();
}

// Check if role is set and valid
$valid_roles = ['student', 'teacher'];
$role = isset($_GET['role']) ? $_GET['role'] : '';
if (!in_array($role, $valid_roles)) {
    header('Location: ../views/admin_dashboard.php');
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect user data from the form
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Initialize User model
    $userModel = new User();

    // Check if username or email already exists
    if ($userModel->usernameExists($username)) {
        $message = 'Username already exists.';
    } elseif ($userModel->emailExists($email)) {
        $message = 'Email already exists.';
    } elseif ($password !== $confirm_password) {
        $message = 'Passwords do not match.';
    } else {
        // Create the user
        if ($userModel->createUser($username, $email, $password, $role)) {
            $message = 'User created successfully.';
        } else {
            $message = 'Failed to create user.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
    <link rel="stylesheet" href="../assets/css/components/form.css">
    <link rel="stylesheet" href="../assets/css/form_center.css">
</head>
<body>
    <div class="container">
        <h1>Create <?php echo htmlspecialchars($role); ?></h1>
        <?php if ($message): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <form method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            
            <button type="submit">Create User</button>
        </form>
    </div>
</body>
</html>
