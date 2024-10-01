<?php
require_once '../config/database.php'; 

class  Assignment{
    private $db;

    public function __construct() {
        global $conn;
        $this->db = $conn;
    }

    // Create a new assignment
    public function createAssignment($classroomId, $teacherId, $Title, $description, $dueDate) {
        $query = "INSERT INTO assignments (classroom_id, teacher_id, assignment_title, description, due_date) 
                  VALUES ('$classroomId', '$teacherId', '$Title', '$description', '$dueDate')";
        $result = mysqli_query($this->db, $query);
        return $result ? mysqli_insert_id($this->db) : false;
    }

    // Get assignments for a classroom
    public function getAssignmentsForClassroom($classroomId) {
        $query = "SELECT * FROM assignments WHERE classroom_id = $classroomId";
        $result = mysqli_query($this->db, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC); 
    }
    
    
    // Save student's answer
    public function saveOrUpdateStudentAssignment($assignmentId, $studentId, $filePath) {
        $query = "SELECT * FROM student_assignments WHERE assignment_id = $assignmentId AND student_id = $studentId";
        $result = mysqli_query($this->db, $query);
        if (mysqli_num_rows($result) > 0) {
            
            $query = "UPDATE student_assignments SET submission_file_path = '$filePath' , submitted_at = NOW(),grade = NULL, feedback = NULL  WHERE assignment_id = $assignmentId AND student_id = $studentId";
        } else {
            
            $query = "INSERT INTO student_assignments (assignment_id, student_id, submission_file_path) VALUES ($assignmentId, $studentId, '$filePath')";
        }
        
        return mysqli_query($this->db, $query);
    }

     // Get the student's submission for a specific assignment
     public function getStudentSubmission($assignmentId, $studentId) {
        $query = "SELECT * FROM student_assignments WHERE assignment_id = $assignmentId AND student_id = $studentId";
        $result = mysqli_query($this->db, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);  
        }
    
        return null; 
    }

    //Get assignment by Id
    public function getAssignmentById($assignmentId) {
        $query = "SELECT * FROM assignments  WHERE assignment_id = $assignmentId";
        $result = mysqli_query($this->db, $query);
        return mysqli_fetch_assoc($result);
    }

    //Get students for a specific assignment
    public function getStudentsForAssignment($assignmentId) {
        $query = "SELECT 
                    sa.student_id, 
                    u.username AS student_name, 
                    sa.grade, 
                    sa.feedback
                FROM 
                     student_assignments sa
                JOIN 
                     users u ON sa.student_id = u.user_id
                WHERE 
                     sa.assignment_id = $assignmentId";
        $result = mysqli_query($this->db, $query);
        return  mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    //Save student's grade and teacher's feedback
    public function saveGradeAndFeedbackForStudent($assignmentId, $studentId, $grade, $feedback) {
        $query = "UPDATE student_assignments 
              SET grade = $grade, feedback = '$feedback' 
              WHERE assignment_id = $assignmentId AND student_id = $studentId";
        return mysqli_query($this->db, $query);
    }

    //Get student's assignment status
    public function getAssignmentStats($assignmentId, $studentId) {
        $query = "SELECT * FROM student_assignments WHERE assignment_id = $assignmentId AND student_id = $studentId";
        $result = mysqli_query($this->db, $query);
        return mysqli_fetch_assoc($result);
    }
    


}
?>
