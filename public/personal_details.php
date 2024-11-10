<?php

include '../config/database.php';
include '../models/User.php';
include '../includes/back_button.php';

$user_id = $_SESSION['user_id']; // Assuming user_id is stored in the session
$error = '';
$success = '';

$userModel = new User();
$user = $userModel->getUserById($user_id);

// Handle form submission for updating details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_details'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $sql = $userModel->updateUser($user_id, $name, $email, NULL);
    }

    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        // Handle profile picture upload
        $upload_dir = '../assets/uploads/profiles/';
        $file_extension = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid('profile_') . '.' . $file_extension;

        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $upload_dir . $new_filename)) {
            $full_path = $upload_dir . $new_filename;
            $sql = "UPDATE users SET profile_pic_path = '$full_path' WHERE user_id = $user_id";
            if (mysqli_query($conn, $sql)) {
                $_SESSION['profile_pic_path'] = $full_path;
                $success = 'Profile picture updated successfully.';
            } else {
                $error = 'Error updating profile picture: ' . mysqli_error($conn);
            }
        } else {
            $error = 'Error uploading file.';
        }
    }

    if (isset($_POST['delete_pic'])) {
        $default_pic = '../assets/uploads/profiles/default_pic.png';
        $sql = "UPDATE users SET profile_pic_path = '$default_pic' WHERE user_id = $user_id";
        if (mysqli_query($conn, $sql)) {
            $_SESSION['profile_pic_path'] = $default_pic;
            $success = 'Profile picture has been reset to default.';
        } else {
            $error = 'Error resetting profile picture: ' . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Details</title>
    <link rel="stylesheet" href="../assets/css/personal_details.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

<div class="profile-container">
    <div class="profile-pic-container" onclick="toggleEnlarge()">
        <!-- Default profile picture if none exists -->
        <img src="<?= $user['profile_pic_path'] ? $user['profile_pic_path'] : '../assets/default_pic.jpg' ?>" alt="Profile Picture" class="round-pic" id="profile-pic">
        <!-- Edit and Delete buttons (initially hidden) -->
        <div class="profile-buttons" id="profile-buttons">
            <label for="profile_pic" class="edit-icon"><i class="fas fa-pencil-alt"></i></label>
            <form method="POST" enctype="multipart/form-data" class="hide-input">
                <input type="file" name="profile_pic" id="profile_pic" onchange="this.form.submit();">
            </form>
            <form method="POST">
                <button type="submit" name="delete_pic" class="delete-pic-btn">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="details-container">
    <form method="POST" class="update-details-form">
        <!-- Name Field -->
        <div class="detail-row">
            <span class="detail-title">Name:</span>
            <input type="text" name="name" value="<?= htmlspecialchars($user['username']) ?>" readonly id="name_input">
            <button type="button" class="edit-button" onclick="enableEdit('name_input')">
                <i class="fas fa-pencil-alt"></i>
            </button>
        </div>

        <!-- Email Field -->
        <div class="detail-row">
            <span class="detail-title">Email:</span>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" readonly id="email_input">
            <button type="button" class="edit-button" onclick="enableEdit('email_input')">
                <i class="fas fa-pencil-alt"></i>
            </button>
        </div>

        <!-- Account Created At Field (Uneditable) -->
        <div class="detail-row">
            <span class="detail-title">Account created at:</span>
            <span class="uneditable"><?= htmlspecialchars($user['created_at']) ?></span>
        </div>

        <!-- Hidden field to handle the update action -->
        <input type="hidden" name="update_details" value="1">
        <button type="submit" class="save-btn">Save Changes</button>
    </form>

    <!-- Display success or error message -->
    <?php if (isset($success)): ?>
        <p class="success-msg"><?= htmlspecialchars($success) ?></p>
    <?php elseif (isset($error)): ?>
        <p class="error-msg"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
</div>

<script>
function enableEdit(inputId) {
    document.getElementById(inputId).removeAttribute('readonly');
    document.getElementById(inputId).focus();
}

function toggleEnlarge() {
    const profilePic = document.getElementById('profile-pic');
    const buttons = document.getElementById('profile-buttons');
    profilePic.classList.toggle('enlarged');
    buttons.classList.toggle('show-buttons');
}
</script>

</body>
</html>