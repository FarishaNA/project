<?php
session_start();
include '../config/database.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the file was uploaded without errors
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        // Specify the directory for uploads
        $upload_dir = '../assets/uploads/profiles/';
        
        // Get the file extension
        $file_extension = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        
        // Generate a unique filename
        $new_filename = uniqid('profile_') . '.' . $file_extension;

        // Move the uploaded file to the desired directory
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $upload_dir . $new_filename)) {
            // Update the database with the full profile picture path
            $user_id = $_SESSION['user_id']; // Assuming you have user_id stored in the session
            $full_path = $upload_dir . $new_filename; // Store the full path
            $sql = "UPDATE users SET profile_pic_path = '$full_path' WHERE user_id = $user_id";
            
            if (mysqli_query($conn, $sql)) {
                // Update the session with the new profile picture path
                $_SESSION['profile_pic_path'] = $full_path;
                header('Location: personal_details.php?success=Profile picture updated successfully.');
            } else {
                // Handle the error
                echo "Error updating profile picture: " . mysqli_error($conn);
            }
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "File upload error.";
    }
}
?>
