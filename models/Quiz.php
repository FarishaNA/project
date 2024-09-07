<?php
require_once '../config/database.php'; // Include the database connection

class Quiz {
    private $db;

    public function __construct() {
        global $conn;
        $this->db = $conn;
    }

    // Create a new quiz
    public function createQuiz($classroomId, $teacherId, $quizTitle, $description, $quizDate) {
        $query = "INSERT INTO quizzes (classroom_id, teacher_id, quiz_title, description, quiz_date) 
                  VALUES ('$classroomId', '$teacherId', '$quizTitle', '$description', '$quizDate')";

        $result = mysqli_query($this->db, $query);

        if ($result) {
            return mysqli_insert_id($this->db); // Return the ID of the newly inserted quiz
        } else {
            return false; // Return false if the query failed
        }
    }

    // Insert a new question into the quiz
    public function insertQuestion($quizId, $questionText) {
        $query = "INSERT INTO questions (quiz_id, question_text) 
                  VALUES ('$quizId', '$questionText')";

        $result = mysqli_query($this->db, $query);

        if ($result) {
            return mysqli_insert_id($this->db); // Return the ID of the newly inserted question
        } else {
            return false; // Return false if the query failed
        }
    }

    // Insert a new choice for a question
    public function insertChoice($questionId, $choiceText, $isCorrect) {
        $query = "INSERT INTO choices (question_id, choice_text, is_correct) 
                  VALUES ('$questionId', '$choiceText', '$isCorrect')";

        return mysqli_query($this->db, $query); // Return true if the query was successful, otherwise false
    }
}
?>
