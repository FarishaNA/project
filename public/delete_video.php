<?php
require_once '../config/database.php';
require_once '../models/Video.php';

session_start();

$videoId = intval($_GET['id']);
$videoModel = new Video();

$video = $videoModel->getVideoById($videoId);
if ($video && $_SESSION['role'] === 'teacher') {
    // Delete the video file if it exists
    if (file_exists($video['video_file_path'])) {
        unlink($video['video_file_path']);
    }
    // Delete the video record from the database
    $videoModel->deleteVideoById($videoId);
}

// Redirect back to the classroom page
header('Location: ../public/classroom.php?id=' . $_SESSION['selected_classroom']);
exit();

?>
