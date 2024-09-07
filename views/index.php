<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virtual StudySpace</title>
    <link rel="stylesheet" href="../assets/css/components/header.css">
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="main">
    <header>
        <h4>Virtual StudySpace</h4>
        <ul>
            <li><a href="#home">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#features">Features</a></li>
            <li><a href="#contact">Contact Us</a></li>
        </ul>
    </header>

    <div id="home">
        <h3>Virtual StudySpace: Connect, Learn, Thrive</h3>
        <p>Empowering Students and Teachers with Seamless Virtual Classrooms, Real-Time Chat, and Interactive Learning Tools</p>
        <div id="start">
            <a href="register.php" class="cta-button">Get Started</a>
        </div>
    </div>

</div>

    <section id="about" class="about">
        <h2>About Us</h2>
        <p>Virtual StudySpace is a cutting-edge platform designed to bring the classroom experience online. Our goal is to make learning accessible, engaging, and flexible, whether you're a student or an educator. With features like virtual classrooms, real-time communication, and collaborative tools, we aim to create a seamless and interactive learning environment that bridges the gap between traditional and digital education.</p>
        
        <p>Join us in transforming education for the digital age, making it more inclusive and effective for everyone.</p>
    </section>

    <section id="features" class="features">
        <h2>Features</h2>
        <div class="feature-item">
            <img src="../assets/uploads/images/class.jpg" alt="Virtual Classrooms">
            <h3>Virtual Classrooms</h3>
            <p>Create and join virtual classrooms with ease, fostering a collaborative learning environment.</p>
        </div>
        <div class="feature-item">
            <img src="../assets/uploads/images/chat.jpg" alt="Real-Time Chat">
            <h3>Real-Time Chat</h3>
            <p>Engage in real-time communication with classmates and teachers to enhance learning.</p>
        </div>
        <div class="feature-item">
            <img src="../assets/uploads/images/note.jpg" alt="Note Uploading">
            <h3>Note Uploading</h3>
            <p>Upload and share class notes seamlessly with your peers.</p>
        </div>
        <div class="feature-item">
            <img src="../assets/uploads/images/video.jpg" alt="Session Recording">
            <h3>Session Recording</h3>
            <p>Record and revisit class sessions at your convenience.</p>
        </div>
    </section>

    <section id="contact">
        <h2>Contact Us</h2>
        <p>Email us at: <a href="mailto:support@yourapp.com">support@yourapp.com</a></p>
    </section>
   
   <?php include '../includes/footer.php' ?>

   <div id="back-to-top">
    <a href="#" class="back-to-top"><i class="fas fa-chevron-up"></i></a>
</div>
    <script src="index.js"></script>
</body>
</html>
