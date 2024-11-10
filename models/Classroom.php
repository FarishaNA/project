<?php

require_once '../config/database.php'; // Include the database connection
require_once '../models/Assignment.php';
require_once '../models/Quiz.php';

class Classroom {
    private $db;

    public function __construct() {
        global $conn;
        $this->db = $conn;
        $this->assignmentModel = new Assignment();
        $this->quizModel = new Quiz();
    }

    // Create a new classroom
    public function createClassroom($teacherId, $name, $description) {
        $query = "INSERT INTO classrooms (classroom_name, description, teacher_id) VALUES ('$name', '$description', $teacherId)";
        return mysqli_query($this->db, $query);
    }

    // Read/Retrieve a classroom by ID
    public function getClassroomById($classroomId) {
        $query = "SELECT * FROM classrooms WHERE classroom_id = $classroomId";
        $result = mysqli_query($this->db, $query);
        return mysqli_fetch_assoc($result);
    }

    // Retrieve all classrooms for a specific teacher
    public function getAllClassroomsForTeacher($teacherId) {
        $query = "SELECT * FROM classrooms WHERE teacher_id = $teacherId";
        $result = mysqli_query($this->db, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // Retrieve all classrooms (Admin only)
    public function getAllClassrooms() {
        $query = "SELECT * FROM classrooms";
        $result = mysqli_query($this->db, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }


    // Retrieve students by classroom ID
    public function getStudentsByClassroomId($classroomId) {
        $query = "SELECT users.username AS name , users.user_id FROM users 
                  JOIN students ON users.user_id = students.user_id 
                  WHERE students.classroom_id = $classroomId";
        $result = mysqli_query($this->db, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // Retrieve teachers by classroom ID
    public function getTeachersByClassroomId($classroomId) {
        $query = "SELECT users.username AS name FROM users 
                  JOIN classrooms ON users.user_id = classrooms.teacher_id 
                  WHERE classrooms.classroom_id = $classroomId";
        $result = mysqli_query($this->db, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function joinClassroom($studentId, $classroomId) {
        // Check if the student is already in the classroom
        $query = "SELECT * FROM students WHERE user_id = $studentId AND classroom_id = $classroomId";
        $result = mysqli_query($this->db, $query);
    
        if (mysqli_num_rows($result) > 0) {
            return 'already_joined'; // Student is already in the classroom
        }
    
        // Retrieve the student's username from the users table
        $usernameQuery = "SELECT username FROM users WHERE user_id = $studentId";
        $usernameResult = mysqli_query($this->db, $usernameQuery);
    
        if ($usernameResult && mysqli_num_rows($usernameResult) > 0) {
            $row = mysqli_fetch_assoc($usernameResult);
            $username = $row['username'];
    
            // Insert the student into the classroom with their name
            $insertQuery = "INSERT INTO students (user_id, classroom_id, name) VALUES ($studentId, $classroomId, '$username')";
            if (mysqli_query($this->db, $insertQuery)) {
                return 'joined'; // Success
            } else {
                return 'error'; // Database insertion error
            }
        } else {
            return 'user_not_found'; // User not found
        }
    }
    
    
    // Retrieve all classrooms for a specific student
    public function getClassroomsForStudent($studentId) {
        $query = "SELECT c.classroom_id, c.classroom_name,c.teacher_id FROM classrooms c 
                  JOIN students s ON c.classroom_id = s.classroom_id 
                  WHERE s.user_id = $studentId";
        $result = mysqli_query($this->db, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

      // Delete a classroom
      public function deleteClassroom($id) {
        $query = "DELETE FROM classrooms WHERE classroom_id = $id";
        return $this->db->query($query);
    }

    // Update classroom
    public function updateClassroom($id, $name, $description) {
        $query = "UPDATE classrooms SET classroom_name = '$name', description = '$description' WHERE classroom_id = $id";
        return $this->db->query($query);
    }

    // Retrieve student IDs by classroom ID for notifications
    public function getStudentsForNotification($classroomId) {
        $query = "SELECT users.user_id FROM users 
                JOIN students ON users.user_id = students.user_id 
                WHERE students.classroom_id = $classroomId";
        $result = mysqli_query($this->db, $query);

        // Fetch results as a numeric array
        $students = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        // Extract only user_ids into a numeric array
        return array_column($students, 'user_id'); 
    }
 
    // Function to remove student from classroom and delete related records
    public function removeStudentFromClassroom($studentId, $classroomId) {
        // Delete the student from the students table
        $sql = "DELETE FROM students WHERE user_id = $studentId AND classroom_id = $classroomId";
        $this->db->query($sql);

        // Get all quizzes related to the classroom from the Quiz model
        $quizzes = $this->quizModel->getQuizForClassroom($classroomId);
        if (!empty($quizzes)) {
            $quizIds = array_column($quizzes, 'quiz_id'); // Extract quiz IDs
            $quizIdsStr = implode(',', $quizIds);

            // Delete the student's answers related to those quizzes
            $sqlAnswers = "DELETE FROM student_answers WHERE student_id = $studentId AND quiz_id IN ($quizIdsStr)";
            $this->db->query($sqlAnswers);

            // Delete the student's quiz records from students_quizzes
            $sqlQuizzes = "DELETE FROM student_quizzes WHERE student_id = $studentId AND quiz_id IN ($quizIdsStr)";
            $this->db->query($sqlQuizzes);
        }

        // Get all assignments related to the classroom from the Assignment model
        $assignments = $this->assignmentModel->getAssignmentsForClassroom($classroomId);
        if (!empty($assignments)) {
            $assignmentIds = array_column($assignments, 'assignment_id'); // Extract assignment IDs
            $assignmentIdsStr = implode(',', $assignmentIds);

            // Delete the student's assignment submissions related to those assignments
            $sqlAssignments = "DELETE FROM student_assignments WHERE student_id = $studentId AND assignment_id IN ($assignmentIdsStr)";
            $this->db->query($sqlAssignments);
        }

        return true;
    }
     
    public function getClassroomNameById($classroomId) {
        $query = "SELECT classroom_name FROM classrooms WHERE classroom_id = $classroomId";
        $result = $this->db->query($query);
        
        // Fetch the classroom name from the result
        if ($row = $result->fetch_assoc()) {
            return $row['classroom_name'];  // Return the classroom name as a string
        }
        
        return null;  // Return null if no classroom is found
    }
    
    public function getTeacherIdByClassroomId($classroomId) {
        $query = "SELECT teacher_id FROM classrooms WHERE classroom_id = $classroomId";
        $result = $this->db->query($query);
        // Fetch the classroom name from the result
        if ($row = $result->fetch_assoc()) {
            return $row['teacher_id'];  // Return the classroom name as a string
        }
        
        return null;
    }
}
?>
