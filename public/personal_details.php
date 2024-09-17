<?php if (isset($_GET['success'])): ?>
    <p style="color: green;"><?php echo htmlspecialchars($_GET['success']); ?></p>
<?php elseif (isset($_GET['error'])): ?>
    <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
<?php endif; ?>

<form action="upload_profile_pic.php" method="post" enctype="multipart/form-data">
    <label for="profile_pic">Upload Profile Picture:</label>
    <input type="file" name="profile_pic" id="profile_pic" required>
    <button type="submit">Upload</button>
</form>
