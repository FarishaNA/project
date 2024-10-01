<?php
require_once '../config/database.php';
require_once '../models/Quiz.php';
include '../includes/back_button.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../views/login.php');
    exit();
}

$quizId = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;
$studentId = $_SESSION['user_id'];
$quizModel = new Quiz();

$quiz = $quizModel->getQuizById($quizId);

if (!$quiz) {
    echo 'Quiz not found.';
    exit();
}

$questions = $quizModel->getQuestionsForQuiz($quizId);

$attemptsCount = $quizModel->getStudentAttemptsCount($quizId, $studentId);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['role'] == 'student' && $attemptsCount <= 3) {
    $totalScore = 0;
    $totalQuestions = count($questions);

    foreach ($_POST['answers'] as $questionId => $choiceId) {
        $isCorrect = $quizModel->checkIfChoiceIsCorrect($choiceId);
        if ($isCorrect) {
            $totalScore++;
        }
    }

    // Save student quiz attempt and get the attempt_id
    $attemptId = $quizModel->saveStudentQuizAttempt($quizId, $studentId, $totalScore);

    // Now save each student's answer with the attempt_id
    foreach ($_POST['answers'] as $questionId => $choiceId) {
        $quizModel->saveStudentAnswer($studentId, $quizId, $questionId, $choiceId, $attemptId);
    }

    // Redirect to score.php with results as query parameters
    header("Location: score.php?score=$totalScore&total=$totalQuestions");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['role'] == 'teacher') {
    // Handle the form submission for editing questions and choices
    foreach ($_POST['questions'] as $questionId => $questionData) {
        $questionText = $questionData['text'];
        $choices = $questionData['choices'];

        // Update the question
        $quizModel->updateQuestion($questionId, $questionText);

        // Update the choices
        foreach ($choices as $choiceData) {
            $choiceId = $choiceData['id'];
            $choiceText = $choiceData['text'];
            $quizModel->updateChoice($choiceId, $choiceText);
        }
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($quiz['quiz_title']); ?></title>
    <link rel="stylesheet" href="../assets/css/quiz.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <h1><?php echo htmlspecialchars($quiz['quiz_title']); ?></h1>
    <p><?php echo htmlspecialchars($quiz['description']); ?></p>

    <?php if ($_SESSION['role'] == 'student'): ?>
        <!-- Student View -->
        <p>Attempt : <?php echo $attemptsCount+1; ?>/3</p>
        <form method="POST" action="">
            <?php foreach ($questions as $question): ?>
                <div class="question-container">
                    <h4><?php echo htmlspecialchars($question['question_text']); ?></h4>
                    <?php 
                    $choices = $quizModel->getChoicesForQuestion($question['question_id']);
                    foreach ($choices as $choice): ?>
                        <div class="choice">
                            <label>
                                <input type="radio" name="answers[<?php echo $question['question_id']; ?>]" value="<?php echo $choice['choice_id']; ?>" required>
                                <?php echo htmlspecialchars($choice['choice_text']); ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
            <br>
            <?php if ($attemptsCount < 3): ?>
            <input type="submit" value="Submit Quiz">
            <?php else: ?>
                    <p>You have reached the maximum number of attempts for this quiz.</p>
            <?php endif; ?>
        </form>
    <?php elseif ($_SESSION['role'] == 'teacher'): ?>
        <!-- Teacher View -->
        <form method="POST" action="">
            <?php foreach ($questions as $question): ?>
                <div class="question-container">
                    <h4><?php echo htmlspecialchars($question['question_text']); ?></h4>
                    <?php 
                    $choices = $quizModel->getChoicesForQuestion($question['question_id']);
                    foreach ($choices as $choice): ?>
                        <div class="choice">
                            <label>
                                <input type="radio" disabled>
                                <?php echo htmlspecialchars($choice['choice_text']); ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                    <button type="button" id="edit-button" onclick="toggleEdit('edit-container-<?php echo $question['question_id']; ?>')">Edit</button>
                    <div id="edit-container-<?php echo $question['question_id']; ?>" class="edit-container" style="display:none;">
                        <h5>Edit Question</h5>
                        <input type="hidden" name="questions[<?php echo $question['question_id']; ?>][id]" value="<?php echo $question['question_id']; ?>">
                        <input type="text" name="questions[<?php echo $question['question_id']; ?>][text]" value="<?php echo htmlspecialchars($question['question_text']); ?>" required>
                        <h5>Choices</h5>
                        <?php foreach ($choices as $choice): ?>
                            <div class="edit-choice">
                                <input type="hidden" name="questions[<?php echo $question['question_id']; ?>][choices][<?php echo $choice['choice_id']; ?>][id]" value="<?php echo $choice['choice_id']; ?>">
                                <input type="text" name="questions[<?php echo $question['question_id']; ?>][choices][<?php echo $choice['choice_id']; ?>][text]" value="<?php echo htmlspecialchars($choice['choice_text']); ?>" required>
                            </div>
                        <?php endforeach; ?>
                        <button type="submit">Save Changes</button>
                    </div>
                </div>
            <?php endforeach; ?>
            <button type="button" id="final-sub" onclick="window.location.href='view_quizzes.php'">Save & View Quizzes</button>
        </form>

        <div class="attempts">
            <h2>Student Attempts</h2>
            <?php
            $attempts = $quizModel->getStudentAttempts($quizId);
            if ($attempts): ?>
                <table>
                    <tr>
                        <th>Student Name</th>
                        <th>Score</th>
                        <th>Attempt Time</th>
                        <th>Late Attempt</th>
                    </tr>
                    <?php foreach ($attempts as $attempt): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($attempt['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($attempt['score']); ?></td>
                            <td><?php echo htmlspecialchars($attempt['attempted_at']); ?></td>
                            <td><?php echo $attempt['late_attempt'] ? 'Yes' : 'No'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>No attempts found.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <script>
        function toggleEdit(containerId) {
            var container = document.getElementById(containerId);
            if (container.style.display === "none" || container.style.display === "") {
                container.style.display = "block";
            } else {
                container.style.display = "none";
            }
        }
    </script>
</body>
</html>
