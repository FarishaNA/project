<?php
require_once '../config/database.php';
require_once '../models/Assignment.php';
include '../includes/back_button.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../views/login.php');
    exit();
}

$assignmentModel = new Assignment();
$classroomId = isset($_GET['classroom']) ? intval($_GET['classroom']) : 0;
$assignments = $assignmentModel->getAssignmentsForClassroom($classroomId);

// Get the current user's role and ID
$studentId = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Handle assignment upload for students
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_assignment'])) {
    $assignmentId = intval($_POST['assignment_id']);
    
    // Handle file upload
    if (isset($_FILES['submission_file']) && $_FILES['submission_file']['error'] == 0) {
        $fileName = $_FILES['submission_file']['name'];
        $fileTmpPath = $_FILES['submission_file']['tmp_name'];
        $uploadDir = '../assets/uploads/assignments/';
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($fileTmpPath, $filePath)) {
            $assignmentModel->saveOrUpdateStudentAssignment($assignmentId, $studentId, $filePath);
            $successMessage = "Assignment submitted successfully!";
        } else {
            $errorMessage = "Error uploading file!";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Assignments</title>
    <link rel="stylesheet" href="../assets/css/assignments.css">
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
    <h1>Assignments for Classroom <?php echo $classroomId; ?></h1>
    

    <?php if ($role === 'teacher'): ?>
        <button class="add-assignment" onclick="window.location.href='add_assignment.php?classroom=<?php echo $classroomId; ?>';">
            <i class="fas fa-plus"></i>
        </button>
    <?php endif; ?>
        
    <?php if (!empty($assignments)): ?>
        <ul>
            <?php foreach ($assignments as $assignment): ?>
                <li>
                    <h3><?php echo htmlspecialchars($assignment['assignment_title']); ?></h3>
                    <p><?php echo htmlspecialchars($assignment['description']); ?></p>
                    <p>Due Date: <?php echo htmlspecialchars($assignment['due_date']); ?></p>

                    <?php
                    // Check if the student has already submitted the assignment
                    $submission = $assignmentModel->getStudentSubmission($assignment['assignment_id'], $studentId);
                    $isLate = strtotime($assignment['due_date']) < time(); // Check if past the due date
                    ?>

                    <?php if ($role === 'student'): ?>
                        <?php if ($submission): ?>
                            <p><strong>Already Submitted</strong></p>
                            <p>Submission Date: <?php echo htmlspecialchars($submission['submitted_at']); ?></p>
                            <?php if ($isLate): ?>
                                <p style="color: red;"><strong>Late Submission</strong></p>
                            <?php endif; ?>
                            <form action="assignments.php?classroom=<?php echo $classroomId; ?>" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="assignment_id" value="<?php echo $assignment['assignment_id']; ?>">
                                <input type="file" name="submission_file" required>
                                <button type="submit" name="submit_assignment">Resubmit Assignment</button>
                            </form>
                        <?php else: ?>
                            <?php if ($isLate): ?>
                                <p style="color: red;"><strong>Late Submission</strong></p>
                            <?php endif; ?>
                            <!-- File upload form for new submissions -->
                            <form action="assignments.php?classroom=<?php echo $classroomId; ?>" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="assignment_id" value="<?php echo $assignment['assignment_id']; ?>">
                                <input type="file" name="submission_file" required>
                                <button type="submit" name="submit_assignment">Submit Assignment</button>
                            </form>
                        <?php endif; ?>
                    <?php elseif ($role === 'teacher'): ?>
                        <!-- For teachers: Link to grade submissions -->
                        <a href="grade_assignment.php?assignment_id=<?php echo $assignment['assignment_id']; ?>">Grade Submissions</a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No assignments available for this classroom.</p>
    <?php endif; ?>
</div>

</body>
</html>
