<?php
require_once '../config/database.php';
require_once '../models/Quiz.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../views/login.php');
    exit();
}

// Get the quiz ID from the URL
$quizId = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;  // Use 'id' instead of 'classroom'
$quizModel = new Quiz();

// Fetch quiz details based on quiz ID
$quiz = $quizModel->getQuizById($quizId);

if (!$quiz) {
    echo 'Quiz not found.';
    exit();
}

// Fetch questions and choices for the quiz
$questions = $quizModel->getQuestionsForQuiz($quizId);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle form submission and save answers to the student_answers table
    $studentId = $_SESSION['user_id'];
    
    foreach ($_POST['answers'] as $questionId => $choiceId) {
        $quizModel->saveStudentAnswer($studentId, $questionId, $choiceId);
    }
    
    echo 'Your answers have been submitted!';
    exit();  // Prevent the form from being resubmitted upon refreshing
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($quiz['quiz_title']); ?></title>
    <link rel="stylesheet" href="../assets/css/quiz.css">
</head>
<body>
    <h1><?php echo htmlspecialchars($quiz['quiz_title']); ?></h1>
    <p><?php echo htmlspecialchars($quiz['description']); ?></p>

    <!-- Form for quiz questions and choices -->
    <form method="POST" action="">
        <?php foreach ($questions as $question): ?>
            <h3><?php echo htmlspecialchars($question['question_text']); ?></h3>
            <?php 
            $choices = $quizModel->getChoicesForQuestion($question['question_id']); // Get the choices for each question
            foreach ($choices as $choice): ?>
                <label>
                    <input type="radio" name="answers[<?php echo $question['question_id']; ?>]" value="<?php echo $choice['choice_id']; ?>" required>
                    <?php echo htmlspecialchars($choice['choice_text']); ?>
                </label><br>
            <?php endforeach; ?>
        <?php endforeach; ?>
        <br>
        <input type="submit" value="Submit Quiz">
    </form>
</body>
</html>

