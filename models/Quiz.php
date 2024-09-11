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
        return $result ? mysqli_insert_id($this->db) : false;
    }

    // Get quiz details for a classroom
    public function getQuizForClassroom($classroomId) {
        $query = "SELECT * FROM quizzes WHERE classroom_id = $classroomId";
        $result = mysqli_query($this->db, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC); // Check if this returns correct quizzes
    }
    

    // Get questions for a quiz
    public function getQuestionsForQuiz($quizId) {
        $query = "SELECT * FROM questions WHERE quiz_id = $quizId";
        $result = mysqli_query($this->db, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // Get choices for a question
    public function getChoicesForQuestion($questionId) {
        $query = "SELECT * FROM choices WHERE question_id = $questionId";
        $result = mysqli_query($this->db, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // Save student's answer
    public function saveStudentAnswer($studentId, $questionId, $choiceId) {
        $query = "INSERT INTO student_answers (student_id, question_id, choice_id) 
                  VALUES ('$studentId', '$questionId', '$choiceId')";
        return mysqli_query($this->db, $query);
    }

    // Get a quiz by its ID
    public function getQuizById($quizId) {
        $query = "SELECT * FROM quizzes WHERE quiz_id = $quizId";
        $result = mysqli_query($this->db, $query);
        return mysqli_fetch_assoc($result); // Fetch a single row as an associative array
    }

}
?>
