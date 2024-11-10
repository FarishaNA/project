<?php
require_once '../config/database.php';
require_once '../models/Assignment.php';
include '../includes/back_button.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../views/login.php');
    exit();
}

// Get user role and ensure the user is a teacher
$role = $_SESSION['role'];
if ($role !== 'teacher') {
    header('Location: ../views/assignments.php');
    exit();
}

// Create Assignment model instance
$assignmentModel = new Assignment();
$assignmentId = isset($_GET['assignment_id']) ? intval($_GET['assignment_id']) : 0;

// Retrieve assignment details if ID is valid
$assignment = $assignmentModel->getAssignmentById($assignmentId);
if (!$assignment) {
    header('Location: ../views/assignments.php');
    exit();
}

// Handle form submission for updating the assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_assignment'])) {
    $assignmentTitle = trim($_POST['assignment_title']);
    $description = trim($_POST['description']);
    $dueDate = trim($_POST['due_date']);

    if (!empty($assignmentTitle) && !empty($description) && !empty($dueDate)) {
        $result = $assignmentModel->updateAssignment($assignmentId, $assignmentTitle, $description, $dueDate);

        if ($result) {
            header("Location: assignments.php?classroom={$assignment['classroom_id']}");
            exit();
        } else {
            $errorMessage = "Error updating assignment!";
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
    <title>Update Assignment</title>
    <link rel="stylesheet" href="../assets/css/components/form.css">
</head>
<body>

<div class="container">
    <?php if (isset($errorMessage)): ?>
        <div class="error">
            <?php echo htmlspecialchars($errorMessage); ?>
        </div>
    <?php endif; ?>

    <h1>Update Assignment</h1>

    <form action="update_assignment.php?assignment_id=<?php echo $assignmentId; ?>" method="post">
        <label for="assignment_title">Assignment Title:</label>
        <input type="text" id="assignment_title" name="assignment_title" value="<?php echo htmlspecialchars($assignment['assignment_title']); ?>" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($assignment['description']); ?></textarea>

        <label for="due_date">Due Date:</label>
        <input type="date" id="due_date" name="due_date" value="<?php echo htmlspecialchars($assignment['due_date']); ?>" required>

        <button type="submit" name="update_assignment" class="btn">Update Assignment</button>
    </form>
</div>

</body>
</html>
