<?php

require_once '../config/database.php'; // Include the database connection

class Classroom {
    private $db;

    public function __construct() {
        global $conn;
        $this->db = $conn;
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

    // // Update a classroom's details
    // public function updateClassroom($classroomId, $teacherId, $name, $description) {
    //     $query = "UPDATE classrooms SET classroom_name = '$name', description = '$description' WHERE classroom_id = $classroomId AND teacher_id = $teacherId";
    //     return mysqli_query($this->db, $query);
    // }

    // Retrieve students by classroom ID
    public function getStudentsByClassroomId($classroomId) {
        $query = "SELECT users.username AS name FROM users 
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
            return false; // Student is already in the classroom
        }
    
        // Retrieve the student's username from the users table
        $usernameQuery = "SELECT username FROM users WHERE user_id = $studentId";
        $usernameResult = mysqli_query($this->db, $usernameQuery);
    
        if ($usernameResult && mysqli_num_rows($usernameResult) > 0) {
            $row = mysqli_fetch_assoc($usernameResult);
            $username = $row['username'];
    
            // Insert the student into the classroom with their name
            $insertQuery = "INSERT INTO students (user_id, classroom_id, name) VALUES ($studentId, $classroomId, '$username')";
            return mysqli_query($this->db, $insertQuery);
        } else {
            return false; // User not found
        }
    }
    
    // Retrieve all classrooms for a specific student
    public function getClassroomsForStudent($studentId) {
        $query = "SELECT c.classroom_id, c.classroom_name FROM classrooms c 
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
}
?>
