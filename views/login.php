<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Virtual Study Space</title>
    <link rel="stylesheet" href="../assets/css/components/header.css"> 
    <link rel="stylesheet" href="../assets/css/reglog.css"> <!-- Link to your CSS file -->
</head>
<body>
    <header>
        <h4>Virtual Study Space</h4>
        <nav>
            <ul>
                <li><a href="../views/index.html">Home</a></li>
                <li><a href="../views/index.html#about">About</a></li>
                <li><a href="../views/index.html#features">Features</a></li>
                <li><a href="../views/index.html#contact">Contact Us</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section id="login">
            
            <!-- Display the error message if it exists -->
            <?php if (isset($_SESSION['login_error'])): ?>
                <div style="color: red; margin-bottom: 15px;">
                    <?php
                    echo $_SESSION['login_error'];
                    unset($_SESSION['login_error']); // Remove the error after displaying it
                    ?>
                </div>
            <?php endif; ?>

            <h2>Login</h2>
            <form action="../public/login.php" method="post">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Login</button>
            </form>

            <p>Don't have an account? <a href="../views/register.php">Register here</a></p>
        </section>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
