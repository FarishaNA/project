<?php
require_once '../config/database.php';
require_once '../models/Note.php';

session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['note_file']) && isset($_POST['classroom_id']) && isset($_POST['note_title'])) {
    $classroomId = intval($_POST['classroom_id']);
    $teacherId = $_SESSION['user_id'];
    $noteTitle = $_POST['note_title'];

    $file = $_FILES['note_file'];
    $fileName = basename($file['name']);
    $targetDir = "../assets/uploads/notes/";
    $targetFilePath = $targetDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
        $noteModel = new Note();
        $noteModel->addNoteToClassroom($classroomId, $teacherId, $noteTitle, $targetFilePath);
        header('Location: ../public/classroom.php?id=' . $classroomId);
        exit();
    } else {
        echo "Error uploading the file.";
    }
}
?>
