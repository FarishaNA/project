<?php
require_once '../config/database.php';
require_once '../models/Quiz.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../views/login.php');
    exit();
}

$quizId = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;
$quizModel = new Quiz();

$quiz = $quizModel->getQuizById($quizId);

if (!$quiz) {
    echo 'Quiz not found.';
    exit();
}

$questions = $quizModel->getQuestionsForQuiz($quizId);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['role'] == 'student') {
    $studentId = $_SESSION['user_id'];
    $totalScore = 0;
    $totalQuestions = count($questions);

    foreach ($_POST['answers'] as $questionId => $choiceId) {
        $isCorrect = $quizModel->checkIfChoiceIsCorrect($choiceId);
        if ($isCorrect) {
            $totalScore++;
        }
        $quizModel->saveStudentAnswer($studentId, $questionId, $choiceId);
    }

    $scorePercentage = ($totalScore / $totalQuestions) * 100;

    // Save student quiz attempt
    $quizModel->saveStudentQuizAttempt($quizId, $studentId, $totalScore);

    // Redirect to score.php with results as query parameters
    header("Location: score.php?score=$totalScore&total=$totalQuestions");
    exit();
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
    <script>
        function toggleEdit(elementId) {
            const editContainer = document.getElementById(elementId);
            editContainer.style.display = editContainer.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</head>
<body>
    <h1><?php echo htmlspecialchars($quiz['quiz_title']); ?></h1>
    <p><?php echo htmlspecialchars($quiz['description']); ?></p>

    <?php if ($_SESSION['role'] == 'student'): ?>
        <!-- Student View -->
        <form method="POST" action="">
            <?php foreach ($questions as $question): ?>
                <div class="question-container">
                    <h3><?php echo htmlspecialchars($question['question_text']); ?></h3>
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
            <input type="submit" value="Submit Quiz">
        </form>
    <?php elseif ($_SESSION['role'] == 'teacher'): ?>
        <!-- Teacher View -->
        <div class="edit-section">
            <h2>Edit Questions and Choices</h2>
            <?php foreach ($questions as $question): ?>
                <div class="question">
                    <h3>
                        <?php echo htmlspecialchars($question['question_text']); ?>
                        <button type="button" class="edit-button" onclick="toggleEdit('edit-container-<?php echo $question['question_id']; ?>')">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                    </h3>
                    <div id="edit-container-<?php echo $question['question_id']; ?>" class="edit-container" style="display:none;">
                        <form method="POST" action="update_question.php">
                            <input type="hidden" name="question_id" value="<?php echo $question['question_id']; ?>">
                            <input type="text" name="question_text" value="<?php echo htmlspecialchars($question['question_text']); ?>" required>
                            <button type="submit" class="edit-button"><i class="fas fa-save"></i></button>
                        </form>
                    </div>
                    <?php 
                    $choices = $quizModel->getChoicesForQuestion($question['question_id']);
                    foreach ($choices as $choice): ?>
                        <div class="choice">
                            <p><?php echo htmlspecialchars($choice['choice_text']); ?></p>
                            <button type="button" class="edit-button" onclick="toggleEdit('edit-choice-container-<?php echo $choice['choice_id']; ?>')">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            <div id="edit-choice-container-<?php echo $choice['choice_id']; ?>" class="edit-container" style="display:none;">
                                <form method="POST" action="update_choice.php">
                                    <input type="hidden" name="choice_id" value="<?php echo $choice['choice_id']; ?>">
                                    <input type="text" name="choice_text" value="<?php echo htmlspecialchars($choice['choice_text']); ?>" required>
                                    <button type="submit" class="edit-button"><i class="fas fa-save"></i></button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>

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
                            <td><?php echo htmlspecialchars($attempt['attempt_time']); ?></td>
                            <td><?php echo $attempt['late_attempt'] ? 'Yes' : 'No'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>No attempts found.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</body>
</html>
