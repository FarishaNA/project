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
        return mysqli_fetch_all($result, MYSQLI_ASSOC); // Returns all quizzes for the classroom
    }

    // Get a quiz by its ID
    public function getQuizById($quizId) {
        $query = "SELECT * FROM quizzes WHERE quiz_id = $quizId";
        $result = mysqli_query($this->db, $query);
        return mysqli_fetch_assoc($result); // Fetches a single quiz
    }

    // Insert a new question into a quiz
    public function insertQuestion($quizId, $questionText) {
        $query = "INSERT INTO questions (quiz_id, question_text) 
                  VALUES ('$quizId', '$questionText')";
        $result = mysqli_query($this->db, $query);
        return $result ? mysqli_insert_id($this->db) : false;
    }

    // Insert a new choice for a question
    public function insertChoice($questionId, $choiceText, $isCorrect) {
        $query = "INSERT INTO choices (question_id, choice_text, is_correct) 
                  VALUES ('$questionId', '$choiceText', '$isCorrect')";
        return mysqli_query($this->db, $query);
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

    // Save a student's answer
    public function saveStudentAnswer($studentId, $quizId, $questionId, $choiceId, $attemptId) {
        $query = "INSERT INTO student_answers (student_id, quiz_id, question_id, choice_id, attempt_id) 
                  VALUES ('$studentId', '$quizId', '$questionId', '$choiceId', '$attemptId')";
        if (!mysqli_query($this->db, $query)) {
            echo "Error: " . mysqli_error($this->db);
        }
    }
    
    
    // Check if a choice is correct
    public function checkIfChoiceIsCorrect($choiceId) {
        $query = "SELECT is_correct FROM choices WHERE choice_id = $choiceId";
        $result = mysqli_query($this->db, $query);
        $row = mysqli_fetch_assoc($result);
        return $row['is_correct'] == 1;
    }


    // Save a student's quiz attempt
    public function saveStudentQuizAttempt($quizId, $studentId, $score) {
        $query = "INSERT INTO student_quizzes (quiz_id, student_id, score) 
                  VALUES ($quizId, $studentId, $score)";
        if (mysqli_query($this->db, $query)) {
            // Return the last inserted ID (attempt_id)
            return mysqli_insert_id($this->db);
        } else {
            echo "Error: " . mysqli_error($this->db);
            return false;
        }
    }
    
    // Get attempts for a quiz
    public function getStudentAttempts($quizId) {
        $query = "SELECT u.username AS student_name, 
                         sq.score,
                         sq.attempted_at,
                         CASE 
                            WHEN sq.attempted_at > q.quiz_date THEN 1 
                            ELSE 0 
                         END AS late_attempt
                  FROM student_quizzes sq
                  JOIN users u ON sq.student_id = u.user_id
                  JOIN quizzes q ON sq.quiz_id = q.quiz_id
                  WHERE sq.quiz_id = $quizId
                  ORDER BY sq.attempted_at DESC";
        
        $result = mysqli_query($this->db, $query);
        if (!$result) {
            echo "Query Error: " . mysqli_error($this->db);
            return [];
        }
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    

    //Update a single question
    public function updateQuestion($questionId, $questionText) {
        $query = "UPDATE questions SET question_text = '$questionText' WHERE question_id = $questionId";
        mysqli_query($this->db, $query);
    }
    
    //Update a single choice
    public function updateChoice($choiceId, $choiceText) {
        $query = "UPDATE choices SET choice_text = '$choiceText' WHERE choice_id = $choiceId";
        mysqli_query($this->db, $query);
    }

    public function getStudentAttemptsCount($quizId, $studentId) {
        $query = "SELECT COUNT(*) as attempt_count FROM student_quizzes WHERE student_id = $studentId AND quiz_id = $quizId";
        $result = mysqli_query($this->db, $query);
        $row = mysqli_fetch_assoc($result);
        return $row['attempt_count'];
    }

    public function getQuizStats($quizId, $studentId) {
        $query = "SELECT * FROM student_quizzes WHERE quiz_id = $quizId AND student_id = $studentId";
        $result = mysqli_query($this->db, $query);
        return mysqli_fetch_assoc($result);
    }

    public function deleteQuiz($quizId) {
        $deleteQuizQuery = "DELETE FROM quizzes WHERE quiz_id = $quizId";
    return mysqli_query($this->db, $deleteQuizQuery);
    }
}
?>
