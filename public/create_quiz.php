<?php 
require_once '../config/database.php';
require_once '../models/Quiz.php';

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header('Location: ../views/login.php');
    exit();
}

$teacher_id = $_SESSION['user_id'];
$classroom_id = $_SESSION['selected_classroom']; 
$quizCreated = isset($_POST['quiz_created']) ? $_POST['quiz_created'] : false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (!$quizCreated) {
            // Handle the quiz creation form
            $classroom_id = $_SESSION['selected_classroom']; // Using session for classroom ID
            $quiz_title = isset($_POST['quiz_title']) ? $_POST['quiz_title'] : '';
            $description = isset($_POST['description']) ? $_POST['description'] : '';
            $quiz_date = isset($_POST['quiz_date']) ? $_POST['quiz_date'] : '';
    
            // Proceed only if the required fields are filled
            if ($quiz_title && $description && $quiz_date) {
                $quiz = new Quiz();
                $quizId = $quiz->createQuiz($classroom_id, $teacher_id, $quiz_title, $description, $quiz_date);
    
                if ($quizId) {
                    $quizCreated = true;
                } else {
                    $error = "Error creating quiz, try again.";
                }
            } else {
                $error = "Please fill all required fields.";
            }
        }
    
    } elseif (isset($_POST['add_question'])) {
        // Handle the question and choice creation form
        $quizId = $_POST['quiz_id'];
        $questionText = $_POST['question_text'];
        $choices = $_POST['choices'];
        $correct_choice = $_POST['correct_choice'];

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
    } elseif (isset($_POST['final_submit'])) {
        // Handle the final submission of the quiz
        $quizCreated = false;
        $success = "Quiz created successfully and submitted!";
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
            return confirm("Are you sure there are no more questions to add?");
        }
    </script>
    <link rel="stylesheet" href="../assets/css/components/form.css">
    <link rel="stylesheet" href="../assets/css/form_center.css">
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

            <input type="submit" value="Create Quiz">
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
            <input type="submit" name="final_submit" value="Submit Quiz" onclick="return confirmSubmit();">
        </form>
        </div>
    <?php endif; ?>

</body>
</html>
