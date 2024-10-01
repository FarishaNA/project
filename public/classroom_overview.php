<?php
require_once '../config/database.php';
require_once '../models/User.php';
require_once '../models/Assignment.php';
require_once '../models/Quiz.php';
include '../includes/back_button.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../views/login.php');
    exit();
}

$classroomId = isset($_GET['classroom']) ? intval($_GET['classroom']) : 0;
$teacherId = $_SESSION['user_id']; // Assuming the teacher is logged in

$userModel = new User();
$assignmentModel = new Assignment();
$quizModel = new Quiz();

// Get list of students in the classroom
$students = $userModel->getStudentsByClassroom($classroomId);

// Get assignment submissions and quizzes
$assignments = $assignmentModel->getAssignmentsForClassroom($classroomId);
$quizzes = $quizModel->getQuizForClassroom($classroomId);

// Prepare data for charts
$studentStats = [];
foreach ($students as $student) {
    $studentId = $student['user_id'];
    $studentStats[$studentId] = [
        'name' => $student['name'],
        'assignmentsAttempted' => 0,
        'assignmentsSubmitted' => 0,
        'quizzesAttempted' => 0,
        'lateSubmissions' => 0
    ];

    foreach ($assignments as $assignment) {
        $stats = $assignmentModel->getAssignmentStats($assignment['assignment_id'], $studentId);
        if ($stats) {
            $studentStats[$studentId]['assignmentsAttempted']++;
            if ($stats['submitted_at']) {
                $studentStats[$studentId]['assignmentsSubmitted']++;
                if (strtotime($assignment['due_date']) < strtotime($stats['submitted_at'])) {
                    $studentStats[$studentId]['lateSubmissions']++;
                }
            }
        }
    }

    foreach ($quizzes as $quiz) {
        $stats = $quizModel->getQuizStats($quiz['quiz_id'], $studentId);
        if ($stats) {
            $studentStats[$studentId]['quizzesAttempted']++;
        }
    }
}

// Convert data to JSON format for JavaScript
$labels = array_values(array_map(fn($s) => $s['name'], $studentStats));
$assignmentsData = array_values(array_map(fn($s) => $s['assignmentsAttempted'], $studentStats));
$quizzesData = array_values(array_map(fn($s) => $s['quizzesAttempted'], $studentStats));
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classroom Overview</title>
    <link rel="stylesheet" href="../assets/css/classroom_overview.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            width: 300px;
            height: 300px;
            margin: 20px;
        }
        .students-container {
            display: flex;
            flex-wrap: wrap;
        }
        .student-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin: 10px;
            width: 250px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .student-header h3 {
            margin: 0;
        }
        .stat {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .stat i {
            margin-right: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Classroom Overview for Classroom <?php echo htmlspecialchars($classroomId); ?></h1>

    <div class="charts-container">
        <div class="chart-container">
            <h2>Assignments Attempted by Students</h2>
            <canvas id="assignmentsChart"></canvas>
        </div>
        <div class="chart-container">
            <h2>Quizzes Attempted by Students</h2>
            <canvas id="quizzesChart"></canvas>
        </div>
    </div>

    <h2>Students List</h2>
    <div class="students-container">
        <?php foreach ($studentStats as $studentId => $stats): ?>
            <div class="student-card">
                <div class="student-header">
                    <h3><?php echo htmlspecialchars($stats['name']); ?></h3>
                </div>
                <div class="student-body">
                    <div class="stat">
                        <i class="fas fa-tasks"></i>
                        <p>Assignments Attempted: <?php echo $stats['assignmentsAttempted']; ?></p>
                    </div>
                    <div class="stat">
                        <i class="fas fa-check"></i>
                        <p>Assignments Submitted: <?php echo $stats['assignmentsSubmitted']; ?></p>
                    </div>
                    <div class="stat">
                        <i class="fas fa-clipboard-check"></i>
                        <p>Quizzes Attempted: <?php echo $stats['quizzesAttempted']; ?></p>
                    </div>
                    <div class="stat">
                        <i class="fas fa-clock"></i>
                        <p>Late Submissions: <?php echo $stats['lateSubmissions']; ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctxAssignments = document.getElementById('assignmentsChart').getContext('2d');
        const ctxQuizzes = document.getElementById('quizzesChart').getContext('2d');

        const labels = <?php echo json_encode($labels); ?>;
        const assignmentsData = <?php echo json_encode($assignmentsData); ?>;
        const quizzesData = <?php echo json_encode($quizzesData); ?>;

        function generateColors(count) {
        const colors = [];
        for (let i = 0; i < count; i++) {
            // Generate random RGB values for a subtle palette
            const r = Math.floor(Math.random() * 80 + 100); // Values between 100 and 180
            const g = Math.floor(Math.random() * 80 + 150); // Values between 150 and 230
            const b = Math.floor(Math.random() * 80 + 170); // Values between 170 and 250
            const alpha = 0.4; // Transparency value

            colors.push(`rgba(${r}, ${g}, ${b}, ${alpha})`);
        }
        return colors;
    }

    // Get dynamic colors based on the number of students
    const backgroundColorsAssignments = generateColors(assignmentsData.length);
    const backgroundColorsQuizzes = generateColors(quizzesData.length);

    new Chart(ctxAssignments, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                label: 'Assignments Attempted',
                data: assignmentsData,
                backgroundColor: backgroundColorsAssignments,
                borderColor: 'rgba(0, 0, 0, 0.1)', // Subtle border color
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw;
                        }
                    }
                }
            }
        }
    });

    new Chart(ctxQuizzes, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                label: 'Quizzes Attempted',
                data: quizzesData,
                backgroundColor: backgroundColorsQuizzes,
                borderColor: 'rgba(0, 0, 0, 0.1)', // Subtle border color
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw;
                        }
                    }
                }
            }
        }
    });
});
</script>


</body>
</html>
