<?php
require_once '../config/database.php';
require_once '../models/Video.php';

session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['video_file']) && isset($_POST['classroom_id']) && isset($_POST['video_title'])) {
    $classroomId = intval($_POST['classroom_id']);
    $teacherId = $_SESSION['user_id'];
    $videoTitle = $_POST['video_title'];

    $file = $_FILES['video_file'];
    $fileName = basename($file['name']);
    $targetDir = "../assets/uploads/videos/";
    $targetFilePath = $targetDir . $fileName;

    // Check if the upload was successful
    if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
        $videoModel = new Video();
        $videoModel->addVideoToClassroom($classroomId, $teacherId, $videoTitle, $targetFilePath);
        header('Location: ../public/classroom.php?id=' . $classroomId);
        exit();
    } else {
        echo "Error uploading the video file.";
    }
}
?>
