<?php
require_once '../config/database.php';
require_once '../models/Video.php';
include '../includes/back_button.php';

// Check if the user is a teacher
if ($_SESSION['role'] !== 'teacher') {
    header('Location: ../views/login.php');
    exit();
}

$videoId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$videoModel = new Video();
$video = $videoModel->getVideoById($videoId);

// Redirect if the video doesn't exist
if (!$video) {
    echo 'Video not found.';
    exit();
}

// Handle form submission for updating the video
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $videoTitle = htmlspecialchars($_POST['video_title']);
    $videoFile = $_FILES['video_file'];

    // Check if the form has a new file to replace the old one
    if ($videoFile['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/videos/';  // Ensure this directory exists
        $fileName = basename($videoFile['name']);
        $uploadFilePath = $uploadDir . $fileName;

        // Delete the old file if a new file is uploaded
        if (file_exists($video['video_file_path'])) {
            unlink($video['video_file_path']);
        }

        // Move the uploaded file to the correct directory
        if (move_uploaded_file($videoFile['tmp_name'], $uploadFilePath)) {
            // Update video title and file path
            $videoModel->updateVideo($videoId, $videoTitle, $uploadFilePath);
        } else {
            echo 'Failed to upload the new video file.';
            exit();
        }
    } else {
        // Update only the video title if no file is uploaded
        $videoModel->updateVideoTitle($videoId, $videoTitle);
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
    <title>Edit Video</title>
    <link rel="stylesheet" href="../assets/css/components/form.css">
</head>
<body>
    <div class="container">
        <h1>Edit Video</h1>

        <form action="edit_video.php?id=<?php echo $videoId; ?>" method="POST" enctype="multipart/form-data">
            <label for="video_title">Video Title</label>
            <input type="text" name="video_title" id="video_title" value="<?php echo htmlspecialchars($video['video_title']); ?>" required>

            <label for="video_file">Replace File (optional)</label>
            <input type="file" name="video_file" id="video_file" accept="video/*">

            <button type="submit">Save Changes</button>
        </form>
    </div>
</body>
</html>
