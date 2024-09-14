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

    //check if choice is correct
    public function checkIfChoiceIsCorrect($choiceId) {
        $query = "SELECT * FROM choices WHERE choice_id=$choiceId";
        $result = mysqli_query($this->db, $query);
        $result = mysqli_fetch_assoc($result);
        if($result['is_correct']==1)
           return true;
        else
           return false;
    }

    //save student quiz attempt
    public function saveStudentQuizAttempt($quizId, $studentId, $score) {
        $query = "INSERT INTO student_quizzes(quiz_id, student_id, score)
        VALUES ($quizId, $studentId, $score)";
        return mysqli_query($this->db, $query);
    }

    public function getStudentAttempts($quizId) {
        // Prepare the SQL query
        $query = "SELECT sa.*, u.username AS student_name
                  FROM student_quizzes sq
                  JOIN student_answers sa ON sq.student_id = sa.student_id
                  JOIN users u ON sa.student_id = u.user_id
                  WHERE sq.quiz_id = $quizId
                  GROUP BY sa.student_id";
    
        // Execute the query
        $result = mysqli_query($this->db, $query);
    
        // Check if query execution was successful
        if (!$result) {
            // Output error information for debugging
            echo "Query Error: " . mysqli_error($this->db);
            return [];
        }
    
        // Fetch all results as an associative array
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    

}
?>
