<?php
session_start(); // Start the session to retrieve error messages

// Retrieve error message and form data from session
$error = isset($_SESSION['registration_error']) ? $_SESSION['registration_error'] : '';
$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : array();

// Clear session variables after use
unset($_SESSION['registration_error']);
unset($_SESSION['form_data']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Virtual Study Space</title>
    <link rel="stylesheet" href="../assets/css/components/header.css">
    <link rel="stylesheet" href="../assets/css/components/form.css"> <!-- Link to your CSS file -->
</head>
<body>
    <header>
        <h4>Virtual Study Space</h4>
        <nav>
            <ul>
                <li><a href="../views/index.php">Home</a></li>
                <li><a href="../views/index.php#about">About</a></li>
                <li><a href="../views/index.php#features">Features</a></li>
                <li><a href="../views/index.php#contact">Contact Us</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section id="register">
            
            
            <!-- Display the error message if it exists -->
            <?php if (!empty($error)): ?>
                <div style="color: red; margin-bottom: 15px;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <h2>Register</h2>
            <form action="../public/register.php" method="post">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($form_data['username'] ?? ''); ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>

                <label for="role">Role:</label>
                <select id="role" name="role" required>
                    <option value="student" <?php echo isset($form_data['role']) && $form_data['role'] === 'student' ? 'selected' : ''; ?>>Student</option>
                    <option value="teacher" <?php echo isset($form_data['role']) && $form_data['role'] === 'teacher' ? 'selected' : ''; ?>>Teacher</option>
                </select>

                <button type="submit">Register</button>
            </form>

            <p>Already have an account? <a href="../views/login.php">Login here</a></p>
        </section>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
