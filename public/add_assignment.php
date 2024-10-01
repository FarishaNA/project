<?php
require_once '../config/database.php';
require_once '../models/Assignment.php';
include '../includes/back_button.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../views/login.php');
    exit();
}

// Get the current user's role and ID
$role = $_SESSION['role'];
$teacherId = $_SESSION['user_id'];

// Redirect if the user is not a teacher
if ($role !== 'teacher') {
    header('Location: ../views/assignments.php');
    exit();
}

$assignmentModel = new Assignment();
$classroomId = isset($_GET['classroom']) ? intval($_GET['classroom']) : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_assignment'])) {
    $assignmentTitle = trim($_POST['assignment_title']);
    $description = trim($_POST['description']);
    $dueDate = trim($_POST['due_date']);

    if (!empty($assignmentTitle) && !empty($description) && !empty($dueDate)) {
        $result = $assignmentModel->createAssignment($classroomId, $teacherId, $assignmentTitle, $description, $dueDate);

        if ($result) {
            $successMessage = "Assignment created successfully!";
        } else {
            $errorMessage = "Error creating assignment!";
        }
    } else {
        $errorMessage = "All fields are required!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Assignment</title>
    <link rel="stylesheet" href="../assets/css/components/form.css">
    <link rel="stylesheet" href="../assets/css/form_center.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<div class="container">
    <?php if (isset($successMessage)): ?>
        <div class="success">
            <?php echo htmlspecialchars($successMessage); ?>
        </div>
    <?php elseif (isset($errorMessage)): ?>
        <div class="error">
            <?php echo htmlspecialchars($errorMessage); ?>
        </div>
    <?php endif; ?>

    <h1>Add Assignment</h1>

    <form action="add_assignment.php?classroom=<?php echo $classroomId; ?>" method="post">
        <label for="assignment_title">Assignment Title:</label>
        <input type="text" id="assignment_title" name="assignment_title" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="4" required></textarea>

        <label for="due_date">Due Date:</label>
        <input type="date" id="due_date" name="due_date" required>

        <button type="submit" name="create_assignment" class="submit-button">Create Assignment</button>
    </form>
</div>

</body>
</html>
