<?php

session_start();
require_once '../config/database.php';
require_once '../models/User.php';
require_once '../models/Classroom.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../views/login.php');
    exit();
}

if (isset($_GET['action']) && isset($_GET['type']) && isset($_GET['id'])) {
    $action = $_GET['action']; // update or delete
    $type = $_GET['type']; // students, teachers, classrooms
    $id = $_GET['id'];
        
} else {
    echo "Invalid parameters.";
    exit();
}

// Initialize $item to avoid undefined variable warnings
$item = null;

// Handle deletion
if ($action === 'dlt') {
    if ($type === 'students' || $type === 'teachers') {
        $userModel = new User();
        $userModel->deleteUser($id);  // Delete user function
    } elseif ($type === 'classrooms') {
        $classroomModel = new Classroom();
        $classroomModel->deleteClassroom($id);  // Delete classroom function
    }
    header('Location: management.php?type=' . $type);  // Redirect after delete
    exit();
}

// For update action
if ($action === 'up') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // For students and teachers
        if ($type === 'students' || $type === 'teachers') {
            $name = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];

            // Only update password if provided
            $hashedPassword = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : null;

            $userModel = new User();
            $userModel->updateUser($id, $name, $email, $hashedPassword); // Update function

        } elseif ($type === 'classrooms') {
            // For classrooms
            $classroomName = $_POST['classroom_name'];
            $description = $_POST['description'];

            $classroomModel = new Classroom();
            $classroomModel->updateClassroom($id, $classroomName, $description); // Update function
        }

        // Redirect after update
        header('Location: management.php?type=' . $type);
        exit();
    } else {
        if ($type === 'students' || $type === 'teachers') {
            $userModel = new User();
            $item = $userModel->getUserById($id);
        } elseif ($type === 'classrooms') {
            $classroomModel = new Classroom();
            $item = $classroomModel->getClassroomById($id);
        }

        // Check if item was found
        if ($item === null) {
            echo "Error: No item found.";
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update <?php echo $type === 'classrooms' ? 'Classroom' : 'User'; ?></title>
</head>
<body>
    <h1>Update <?php echo $type === 'classrooms' ? 'Classroom' : 'User'; ?></h1>

    <form action="" method="POST">
        <?php if ($type === 'students' || $type === 'teachers'): ?>
            <label for="username">Name:</label>
            <input type="text" name="username" value="<?php echo isset($item['username']) ? htmlspecialchars($item['username']) : ''; ?>" required><br>

            <label for="email">Email:</label>
            <input type="email" name="email" value="<?php echo isset($item['email']) ? htmlspecialchars($item['email']) : ''; ?>" placeholder="Leave empty to keep current password"><br>

            <label for="password">Password:</label>
            <input type="password" name="password" placeholder="Leave empty to keep current password"><br>
        
        <?php elseif ($type === 'classrooms'): ?>
            
            <label for="classroom_name">Classroom Name:</label>
            <input type="text" name="classroom_name" value="<?php echo isset($item['classroom_name']) ? htmlspecialchars($item['classroom_name']) : ''; ?>" required><br>

            <label for="description">Description:</label>
            <textarea name="description" required><?php echo isset($item['description']) ? htmlspecialchars($item['description']) : ''; ?></textarea><br>
        <?php endif; ?>

        <input type="submit" value="Update">
    </form>
</body>
</html>
