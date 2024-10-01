<?php
require_once '../config/database.php';
require_once '../models/Quiz.php';
include '../includes/back_button.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../views/login.php');
    exit();
}

$quiz = new Quiz();
$classroomId = isset($_SESSION['selected_classroom']) ? intval($_SESSION['selected_classroom']) : 0; 
$quizzes = $quiz->getQuizForClassroom($classroomId);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classroom Quizzes</title>
    <link rel="stylesheet" href="../assets/css/student_dashboard.css">
    <link rel="stylesheet" href="../assets/font-awesome/css/all.min.css">
</head>
<body>

    <div class="container">
        <h1>Your Quizzes</h1>
        <div class="classroom-list">
            <?php if (!empty($quizzes)): ?>
                <?php foreach ($quizzes as $q): ?>
                    <div class="classroom">
                        <div class="options">
                            <span class="dot">•••</span>
                            <div class="dropdown">
                                <a href="../public/quiz.php?quiz_id=<?php echo $q['quiz_id']; ?>">View Quiz</a>
                            </div>
                        </div>
                        <!-- Corrected the link to match the quiz_id parameter -->
                        <a href="../public/quiz.php?quiz_id=<?php echo $q['quiz_id']; ?>">
                            <?php echo htmlspecialchars($q['quiz_title']); ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No quizzes available for this classroom.</p>
            <?php endif; ?>
        </div>

        <?php if($_SESSION['role'] === "teacher"): ?>
            <button class="join-classroom" onclick="window.location.href='../public/create_quiz.php';">
                
            </button>
            <div class="join-classroom-tooltip">Create Quiz</div>
        <?php endif; ?>
    </div>

</body>
</html>
