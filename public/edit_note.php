<?php
require_once '../config/database.php';
require_once '../models/Note.php';
include '../includes/back_button.php';


// Check if the user is a teacher
if ($_SESSION['role'] !== 'teacher') {
    header('Location: ../views/login.php');
    exit();
}

$noteId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$noteModel = new Note();
$note = $noteModel->getNoteById($noteId);

// Redirect if the note doesn't exist
if (!$note) {
    echo 'Note not found.';
    exit();
}

// Handle form submission for updating the note
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $noteTitle = htmlspecialchars($_POST['note_title']);
    $noteFile = $_FILES['note_file'];

    // Check if the form has a new file to replace the old one
    if ($noteFile['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        $fileName = basename($noteFile['name']);
        $uploadFilePath = $uploadDir . $fileName;

        // Delete the old file if a new file is uploaded
        if (file_exists($note['note_file_path'])) {
            unlink($note['note_file_path']);
        }

        // Move the uploaded file to the correct directory
        move_uploaded_file($noteFile['tmp_name'], $uploadFilePath);

        // Update note title and file path
        $noteModel->updateNote($noteId, $noteTitle, $uploadFilePath);
    } else {
        // Update only the note title if no file is uploaded
        $noteModel->updateNoteTitle($noteId, $noteTitle);
    }

    // Redirect back to the classroom page
    header('Location: classroom.php?id=' . $_SESSION['selected_classroom']);
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Note</title>
    <link rel="stylesheet" href="../assets/css/components/form.css">
</head>
<body>
    <div class="container">
    <h1>Edit Note</h1>

    <form action="edit_note.php?id=<?php echo $noteId; ?>" method="POST" enctype="multipart/form-data">
        <label for="note_title">Note Title</label>
        <input type="text" name="note_title" id="note_title" value="<?php echo htmlspecialchars($note['note_title']); ?>" required>

        <label for="note_file">Replace File (optional)</label>
        <input type="file" name="note_file" id="note_file">

        <button type="submit">Save Changes</button>
    </form>
    </div>
</body>
</html>
