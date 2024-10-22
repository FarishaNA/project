<?php
class Feedback {
    private $db;

    public function __construct() {
        global $conn;
        $this->db = $conn;

        // Check for connection errors
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    // Insert feedback into the database
    public function insertFeedback($user_id, $message, $receiver_id, $classroom_id) {
       
        if($classroom_id != null) {
             $query = "INSERT INTO feedback (user_id, feedback_text, receiver_id, classroom_id) VALUES ($user_id, '$message', $receiver_id, $classroom_id)";
        }
        else {
            $query = "INSERT INTO feedback (user_id, feedback_text, receiver_id) VALUES ($user_id, '$message', $receiver_id)";
        }
        
        return $this->db->query($query);
    }

    // Fetch feedback from the database
    public function fetchFeedback($user_id) {
            
        $query = "SELECT * FROM feedback WHERE receiver_id = $user_id ORDER BY submitted_at DESC";

        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Delete feedback by ID (optional)
    public function deleteFeedback($feedback_id) {
        $query = "DELETE FROM feedback WHERE feedback_id = $feedback_id"; // Ensure the correct column name is used
        return $this->db->query($query);
    }

    // Fetch enrolled classrooms and their respective teachers for students
    public function getEnrolledClassrooms($user_id) {
        $query = "SELECT classrooms.classroom_id, classrooms.teacher_id FROM classrooms
                  JOIN students ON classrooms.classroom_id = students.classroom_id
                  WHERE students.user_id = $user_id";

        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getUserfeedbacks($user_id) {
        $query = "SELECT * FROM feedback WHERE user_id = $user_id ORDER BY submitted_at DESC";

        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
