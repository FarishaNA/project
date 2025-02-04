<?php
require_once '../config/database.php';
require_once '../models/Note.php';

session_start();

    $noteId = intval($_GET['id']);
    $noteModel = new Note();

    $note = $noteModel->getNoteById($noteId);
    if ($note && $_SESSION['role'] === 'teacher') {
        if (file_exists($note['note_file_path'])) {
            unlink($note['note_file_path']);
        }
        $noteModel->deleteNoteById($noteId);
    }
    header('Location: ../public/classroom.php?id=' . $_SESSION['selected_classroom']);
    exit();

?>
