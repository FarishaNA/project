<?php
require_once '../config/database.php'; // Include the database connection

class User {
    private $db;

    // Constructor sets up the database connection
    public function __construct() {
        global $conn; // Use the global database connection
        $this->db = $conn;
    }

    // Create a new user
    public function createUser($username, $email, $password, $role) {
        // Simple query without escaping, suitable for learning purposes
        $query = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', '$role')";
        return $this->db->query($query);
    }

    // Get user by username
    public function getUserByUsername($username) {
        $query = "SELECT * FROM users WHERE username = '$username'";
        $result = $this->db->query($query);
        return $result->fetch_assoc();
    }

    public function getUserById($id){
        $query = "SELECT * FROM users WHERE user_id = $id";
        $result =$this->db->query($query);
        return $result->fetch_assoc();
    }

      // Check if username exists
      public function usernameExists($username) {
        return $this->getUserByUsername($username) !== null;
    }

    // Check if email exists
    public function emailExists($email) {
        return $this->getUserByEmail($email) !== null;
    }
    
    // Get user by email
    public function getUserByEmail($email) {
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = $this->db->query($query);
        return $result->fetch_assoc();
    }

    // Get all students
    public function getAllStudents() {
        $query = "SELECT username ,user_id FROM users WHERE role = 'student'";
        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get all teachers
    public function getAllTeachers() {
        $query = "SELECT username ,user_id FROM users WHERE role = 'teacher'";
        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
