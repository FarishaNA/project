<?php
require_once '../config/database.php';

class Video {
    private $db;

    public function __construct() {
        global $conn;
        $this->db = $conn;
    }

    // Add video to classroom
    public function addVideoToClassroom($classroomId, $teacherId, $videoTitle, $filePath) {
        $query = "INSERT INTO videos (classroom_id, teacher_id, video_title, video_file_path) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('iiss', $classroomId, $teacherId, $videoTitle, $filePath);
        return $stmt->execute();
    }

    // Fetch videos for a specific classroom
    public function getVideosByClassroomId($classroomId) {
        $query = "SELECT * FROM videos WHERE classroom_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $classroomId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Fetch specific video by ID
    public function getVideoById($videoId) {
        $query = "SELECT * FROM videos WHERE video_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $videoId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Delete a video by ID
    public function deleteVideoById($videoId) {
        $query = "DELETE FROM videos WHERE video_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $videoId);
        return $stmt->execute();
    }

    // Update a video's title
    public function updateVideoTitle($videoId, $newTitle) {
        $query = "UPDATE videos SET video_title = ? WHERE video_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('si', $newTitle, $videoId);
        return $stmt->execute();
    }

    // Update a video's title and file path
    public function updateVideo($videoId, $newTitle, $newFilePath) {
        $query = "UPDATE videos SET video_title = ?, video_file_path = ? WHERE video_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ssi', $newTitle, $newFilePath, $videoId);
        return $stmt->execute();
    }
}
