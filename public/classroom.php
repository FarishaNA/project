<?php
require_once '../config/database.php';
require_once '../models/Classroom.php';
require_once '../models/Note.php';
include '../includes/dashboard_header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../views/login.php');
    exit();
}

$classroomModel = new Classroom();
$noteModel = new Note();
$classroomId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$classroom = $classroomModel->getClassroomById($classroomId);

if (!isset($_SESSION['selected_classroom']) || $_SESSION['selected_classroom'] != $classroomId) {
    $_SESSION['selected_classroom'] = $classroomId;

    // Reload the page with a 'refreshed' parameter to prevent infinite loop
    if (!isset($_GET['refreshed'])) {
        header('Location: ' . $_SERVER['PHP_SELF'] . '?id=' . $classroomId . '&refreshed=1');
        exit();
    }
}

if (!$classroom) {
    echo 'Classroom not found.';
    exit();
}

// Fetch students and teachers
$students = $classroomModel->getStudentsByClassroomId($classroomId);
$teachers = $classroomModel->getTeachersByClassroomId($classroomId);

// Fetch notes for the classroom
$notes = $noteModel->getNotesByClassroomId($classroomId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($classroom['classroom_name']); ?></title>
    <link rel="stylesheet" href="../assets/css/classroom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1><?php echo htmlspecialchars($classroom['classroom_name']); ?></h1>
    <p><?php echo htmlspecialchars($classroom['description']); ?></p>

    <div class="tabs">
        <button id="notes-tab" class="active"><i class="fas fa-book"></i><span>Notes</span></button>
        <button id="videos-tab"><i class="fas fa-video"></i><span>Videos</span></button>
        <button id="users-tab"><i class="fas fa-users"></i><span>Users</span></button>
    </div>

    <div id="content">
        <div id="notes" class="tab-content">
            <ul class="notes-list">
                <?php foreach ($notes as $note): ?>
                    <li>
                        <div class="note-title"><?php echo htmlspecialchars($note['note_title']); ?></div>
                        
                        <?php
                        $fileExtension = pathinfo($note['note_file_path'], PATHINFO_EXTENSION);
                        if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
                            echo '<img src="' . htmlspecialchars($note['note_file_path']) . '" alt="Note Preview">';
                        } elseif ($fileExtension === 'pdf') {
                            echo '<embed src="' . htmlspecialchars($note['note_file_path']) . '" type="application/pdf">';
                        } else {
                            echo '<i class="fas fa-file"></i>';
                        }
                        ?>

                        <div class="icon-container">
                            <?php if ($_SESSION['role'] === 'teacher'): ?>
                                <a href="../public/edit_note.php?id=<?php echo $note['note_id']; ?>" class="edit-icon" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="../public/delete_note.php?id=<?php echo $note['note_id']; ?>" class="delete-icon" title="Delete" onclick="return confirm('Are you sure you want to delete this note?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo htmlspecialchars($note['note_file_path']); ?>" class="download-icon" title="Download" download>
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>

            <?php if ($_SESSION['role'] === 'teacher'): ?>
            <button id="add-note-button" title="Add Note"><i class="fas fa-plus"></i></button>
            
            <form id="upload-note-form" action="../public/upload_note.php" method="POST" enctype="multipart/form-data" style="display:none;">
                <input type="hidden" name="classroom_id" value="<?php echo $classroomId; ?>">
                <input type="text" name="note_title" placeholder="Enter note title" required>
                <input type="file" name="note_file" required>
                <button type="submit">Upload Note</button>
            </form>
            <?php endif; ?>
        </div>


        <div id="videos" class="tab-content" style="display: none;">
            <!-- Load videos here -->
        </div>
        <div id="users" class="tab-content" style="display: none;">
            <h2>Teachers</h2>
            <ul>
                <?php foreach ($teachers as $teacher): ?>
                    <li><?php echo htmlspecialchars($teacher['name']); ?></li>
                <?php endforeach; ?>
            </ul>
            <h2>Students</h2>
            <ul>
                <?php foreach ($students as $student): ?>
                    <li><?php echo htmlspecialchars($student['name']); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="bottom-buttons">
        <button id="chat-button"><i class="fas fa-comments"></i><span> Chat</span></button>
        <button id="video-button"><i class="fas fa-video"></i><span>Video</span></button>
    </div>

    <script>
        $(document).ready(function() {
            $('#notes-tab').click(function() {
                $('#notes').show();
                $('#videos').hide();
                $('#users').hide();
                $(this).addClass('active');
                $('#videos-tab').removeClass('active');
                $('#users-tab').removeClass('active');
            });

            $('#videos-tab').click(function() {
                $('#videos').show();
                $('#notes').hide();
                $('#users').hide();
                $(this).addClass('active');
                $('#notes-tab').removeClass('active');
                $('#users-tab').removeClass('active');
            });

            $('#users-tab').click(function() {
                $('#users').show();
                $('#notes').hide();
                $('#videos').hide();
                $(this).addClass('active');
                $('#notes-tab').removeClass('active');
                $('#videos-tab').removeClass('active');
            });

            $('#add-note-button').click(function() {
                $('#upload-note-form').toggle();
            });

            $('#chat-button').click(function() {
                window.location.href = '../public/chat.php';
            });

            $('#video-button').click(function() {
                window.location.href = '../public/video.php';
            });
        });
    </script>
</body>
</html>
