<?php
require_once '../config/database.php';
require_once '../models/Assignment.php';
include '../includes/back_button.php';


// Ensure the user is a teacher
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    die("Unauthorized access.");
}

// Check if the assignment_id is passed
if (!isset($_GET['assignment_id'])) {
    die("Assignment not specified.");
}

$assignmentId = $_GET['assignment_id'];
$assignmentModel = new Assignment();

$assignment = $assignmentModel->getAssignmentById($assignmentId);
$students = $assignmentModel->getStudentsForAssignment($assignmentId);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = $_POST['student_id'];
    $grade = $_POST['grade'];
    $feedback = $_POST['feedback'];

    // Validate that the grade is between 0 and 10
    if (is_numeric($grade) && $grade >= 0 && $grade <= 10) {
        $assignmentModel->saveGradeAndFeedbackForStudent($assignmentId, $studentId, $grade, $feedback);
        header("Location: grade_assignment.php?assignment_id=$assignmentId&success=1");
        exit();
    } else {
        $error = "Please enter a valid grade between 0 and 10.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Grade Assignment</title>
    <link rel="stylesheet" href="../assets/css/grade_assignment.css">
</head>
<body>

<div class="assignment-details">
    <h2>Assignment: <?php echo htmlspecialchars($assignment['assignment_title']); ?></h2>
    <p><?php echo htmlspecialchars($assignment['description']); ?></p>
</div>

<?php if (isset($_GET['success'])): ?>
    <p style="color:green;">Grade and feedback successfully submitted.</p>
<?php endif; ?>

<table class="attempts">
    <thead>
        <tr>
            <th>Student ID</th>
            <th>Student Name</th>
            <th>Grade</th>
            <th>Feedback</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($students as $student): ?>
        <tr>
            <td><?php echo htmlspecialchars($student['student_id']); ?></td>
            <td><?php echo htmlspecialchars($student['student_name']); ?></td>
            <td>
                <?php if ($student['grade'] !== null): ?>
                    <!-- Show the existing grade -->
                    <?php echo htmlspecialchars($student['grade']); ?>
                <?php else: ?>
                    <!-- Show grading input if not graded -->
                    <form method="POST" action="">
                        <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student['student_id']); ?>">
                        <input class="grade-input" type="text" name="grade" placeholder="0-10" required>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($student['feedback'] !== null): ?>
                    <!-- Show existing feedback if provided -->
                    <?php echo htmlspecialchars($student['feedback']); ?>
                <?php else: ?>
                    <!-- Show feedback input if not graded -->
                        <textarea class="feedback-input" name="feedback" placeholder="Enter feedback" rows="1"></textarea>
                        <button class="submit-btn" type="submit">Submit</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>
