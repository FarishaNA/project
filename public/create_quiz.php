<?php 
require_once '../config/database.php';
require_once '../models/Quiz.php';
include '../includes/back_button.php';

// Only teachers can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header('Location: ../views/login.php');
    exit();
}

$teacher_id = $_SESSION['user_id'];
$classroom_id = $_SESSION['selected_classroom']; 
$quizCreated = isset($_SESSION['quiz_created']) ? $_SESSION['quiz_created'] : false; // Store quiz creation state in the session
$quizId = isset($_SESSION['quiz_id']) ? $_SESSION['quiz_id'] : null; // Store quiz ID in session

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Create a new quiz
    if (isset($_POST['quiz_title'])) {
        if (!$quizCreated) {
            // Handle the quiz creation form
            $quiz_title = isset($_POST['quiz_title']) ? $_POST['quiz_title'] : '';
            $description = isset($_POST['description']) ? $_POST['description'] : '';
            $quiz_date = isset($_POST['quiz_date']) ? $_POST['quiz_date'] : '';

            // Proceed only if the required fields are filled
            if ($quiz_title && $description && $quiz_date) {
                $quiz = new Quiz();
                $quizId = $quiz->createQuiz($classroom_id, $teacher_id, $quiz_title, $description, $quiz_date);

                if ($quizId) {
                    $_SESSION['quiz_created'] = true; // Mark quiz as created
                    $_SESSION['quiz_id'] = $quizId; // Store quiz ID in session
                    $quizCreated = true;
                } else {
                    $error = "Error creating quiz, try again.";
                }
            } else {
                $error = "Please fill all required fields.";
            }
        }
    }

    // Add a new question
    if (isset($_POST['add_question'])) {
        // Handle the question and choice creation form
        $questionText = $_POST['question_text'];
        $choices = $_POST['choices'];
        $correct_choice = $_POST['correct_choice'];

        if ($quizId && $questionText && $choices && $correct_choice) {
            $quiz = new Quiz();
            $questionId = $quiz->insertQuestion($quizId, $questionText);

            if ($questionId) {
                foreach ($choices as $index => $choiceText) {
                    $isCorrect = ($index + 1 == $correct_choice) ? 1 : 0;
                    $quiz->insertChoice($questionId, $choiceText, $isCorrect);
                }
                $success = "Question and choices added successfully.";
            } else {
                $error = "Error adding question, try again.";
            }
        } else {
            $error = "Please fill all fields to add a question.";
        }
    }

    // Finalize the quiz submission
    if (isset($_POST['final_submit'])) {
        // Clear the session quiz data
        $_SESSION['quiz_created'] = false;
        $_SESSION['quiz_id'] = null;
        $quizCreated = false;
        $success = "Quiz created successfully and submitted!";

        header("Location: ../public/view_quizzes.php?classroom=$classroom_id");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Quiz</title>
    <script>
        function confirmSubmit() {
            if (confirm("Are you sure there are no more questions to add?")) {
                window.location.href = "view_quizzes.php"; 
            }
        }
    </script>
    <link rel="stylesheet" href="../assets/css/components/form.css">
</head>
<body>

    <?php if (!$quizCreated): ?>
        <!-- Quiz Creation Form -->
        <div class="container">
            <h2>Create a New Quiz</h2>
            <?php if (isset($error)): ?>
                <p style="color: red;"><?php echo $error; ?></p>
            <?php endif; ?>
            <form action="" method="post">
                <label for="quiz_title">Quiz Title:</label>
                <input type="text" id="quiz_title" name="quiz_title" required><br><br>

                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea><br><br>

                <label for="quiz_date">Quiz Date:</label>
                <input type="date" id="quiz_date" name="quiz_date" required><br><br>

                <input type="submit" value="Create Quiz" class="btn">
            </form>
        </div>
    <?php else: ?>
        <!-- Question and Choices Form -->
        <div class="container">
            <h2>Add a New Question</h2>
            <?php if (isset($error)): ?>
                <p style="color: red;"><?php echo $error; ?></p>
            <?php elseif (isset($success)): ?>
                <p style="color: green;"><?php echo $success; ?></p>
            <?php endif; ?>
            <form action="" method="post">
                <label for="question_text">Question:</label>
                <input type="text" id="question_text" name="question_text" required><br><br>

                <label for="choices">Choices:</label><br>
                <input type="text" name="choices[]" required><br>
                <input type="text" name="choices[]" required><br>
                <input type="text" name="choices[]" required><br>
                <input type="text" name="choices[]" required><br><br>

                <label for="correct_choice">Correct Choice (1-4):</label>
                <input type="number" id="correct_choice" name="correct_choice" min="1" max="4" required><br><br>

                <input type="submit" name="add_question" value="Add Question">
            </form>
            <button onclick="confirmSubmit()">Submit Quiz</button>
        </div>
    <?php endif; ?>

</body>
</html>
