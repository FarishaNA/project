<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/dashboard_header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Dashboard</title>
</head>
<body>
    <?php
    session_start();
    require '../models/Notification.php';
    
    $dashboard_link = '';

    if ($_SESSION['role'] === 'admin') {
        $dashboard_link = '../public/admin_dashboard.php';
    } elseif ($_SESSION['role'] === 'teacher') {
        $dashboard_link = '../public/teacher_dashboard.php';
    } elseif ($_SESSION['role'] === 'student') {
        $dashboard_link = '../public/student_dashboard.php';
    }
   
    $path = $_SESSION['profile_pic_path'];
    ?>

    <div class="header">
        <div class="menu">
            <span class="menu-icon" onclick="toggleSidebar()">☰</span>
            <span class="welcome-message">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        </div>
            <img src="<?php echo htmlspecialchars($path); ?>" alt="Profile" class="profile-pic" onclick="window.location.href='../public/personal_details.php'">
            <button class="logout-btn" onclick="confirmLogout()">Logout</button>
        </div>
        <!-- <div class="notification-icon">
            <i class="fas fa-bell"></i>
            <span id="unread-count" class="badge"><?php echo $unreadCount; ?></span>
        </div> -->
    </div>

    <div id="sidebar" class="sidebar">
        <a href="#" class="back-link" onclick="setVar(); return false;">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="../public/personal_details.php">
            <i class="fas fa-user"></i>
            <span>Personal Details</span>
        </a>
        <a href="../public/calendar.php">
            <i class="fas fa-calendar"></i>
            <span>Calendar</span>
        </a>
        <a href="../public/notifications.php">
            <i class="fas fa-bell"></i>
            <span>Notifications</span>
        </a>

        <?php if ($_SESSION['role'] === 'student'): ?>
            <a href="../public/progress.php">
                <i class="fas fa-tasks"></i>
                <span>Progress</span>
            </a>
            <?php if (isset($_SESSION['selected_classroom'])): ?>
                <a href="../public/assignments.php?classroom=<?php echo $_SESSION['selected_classroom']; ?>">
                    <i class="fas fa-book"></i>
                    <span>Assignments</span>
                </a>
                <a href="../public/view_quizzes.php?classroom=<?php echo $_SESSION['selected_classroom']; ?>">
               <i class="fas fa-question"></i>
                <span>Quizzes</span>
                </a>

            <?php endif; ?>
        <?php elseif ($_SESSION['role'] === 'teacher'): ?>
            <?php if (isset($_SESSION['selected_classroom'])): ?>
                <a href="../public/classroom_overview.php?classroom=<?php echo $_SESSION['selected_classroom']; ?>">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Classroom Overview</span>
                </a>
                <a href="../public/assignments.php?classroom=<?php echo $_SESSION['selected_classroom']; ?>">
                    <i class="fas fa-book"></i>
                    <span>Assignments</span>
                </a>
                <a href="../public/view_quizzes.php?classroom=<?php echo $_SESSION['selected_classroom']; ?>">
                <i class="fas fa-question"></i>
                <span>Quizzes</span>
                </a>

            <?php endif; ?>
        <?php endif; ?>


        <a href="../public/feedback.php">
            <i class="fas fa-comments"></i>
            <span>Feedback</span>
        </a>
    </div>

    <script>
    function toggleSidebar() {
        var sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('show');

        // Adjust width and text visibility when sidebar is toggled
        if (sidebar.classList.contains('show')) {
            sidebar.style.width = '250px';
            // Show text
            document.querySelectorAll('.sidebar a span').forEach(function(span) {
                span.style.display = 'inline';
            });
        } else {
            sidebar.style.width = '60px'; // Narrow width for icons only
            // Hide text
            document.querySelectorAll('.sidebar a span').forEach(function(span) {
                span.style.display = 'none';
            });
        }
    }

    function confirmLogout() {
        if (confirm("Are you sure you want to logout?")) {
            window.location.href = '../public/logout.php';
        }
    }

    function setVar() {
        // This will redirect to the PHP script that handles unsetting the session variable if set
        var selectedClassroom = '<?php echo isset($_SESSION['selected_classroom']) ? $_SESSION['selected_classroom'] : ''; ?>';
        if (selectedClassroom) {
            window.location.href = '../public/unset_classroom_var.php';
        } else {
            window.location.href = '<?php echo $dashboard_link; ?>';
        }
    }
    </script>
</body>
</html>
