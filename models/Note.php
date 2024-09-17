<?php
require_once '../config/database.php';
class Note {
    private $db;

    public function __construct() {
        global $conn;
        $this->db = $conn;
    }

    // Add note to classroom
    public function addNoteToClassroom($classroomId, $teacherId, $noteTitle, $filePath) {
        $query = "INSERT INTO notes (classroom_id, teacher_id, note_title, note_file_path) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('iiss', $classroomId, $teacherId, $noteTitle, $filePath);
        return $stmt->execute();
    }

    // Fetch notes for a specific classroom
    public function getNotesByClassroomId($classroomId) {
        $query = "SELECT * FROM notes WHERE classroom_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $classroomId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Fetch specific note by ID
    public function getNoteById($noteId) {
        $query = "SELECT * FROM notes WHERE note_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $noteId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Delete a note by ID
    public function deleteNoteById($noteId) {
        $query = "DELETE FROM notes WHERE note_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $noteId);
        return $stmt->execute();
    }

    // Update a note's title
    public function updateNoteTitle($noteId, $newTitle) {
        $query = "UPDATE notes SET note_title = ? WHERE note_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('si', $newTitle, $noteId);
        return $stmt->execute();
    }
}
