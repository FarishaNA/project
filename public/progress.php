<?php
require_once '../config/database.php';
require_once '../models/User.php';
require_once '../models/Classroom.php';
require_once '../models/Assignment.php';
require_once '../models/Quiz.php';
include '../includes/back_button.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../views/login.php');
    exit();
}

$studentId = $_SESSION['user_id']; // Assuming the logged-in user is the student

$userModel = new User();
$assignmentModel = new Assignment();
$classroomModel = new Classroom();
$quizModel = new Quiz();

// Get student details
$student = $userModel->getUserById($studentId);

// Get all classrooms the student is enrolled in
$classrooms = $classroomModel->getClassroomsForStudent($studentId);

// Prepare data for charts
$classroomStats = []; // Initialize an array to hold stats for each classroom

foreach ($classrooms as $classroom) {
    $classroomId = $classroom['classroom_id'];
    $classroomName = $classroom['classroom_name'];

    // Initialize stats for the classroom
    $classroomStats[$classroomId] = [
        'name' => $classroomName,
        'assignments' => [
            'attempted' => 0,
            'submitted' => 0,
            'lateSubmissions' => 0
        ],
        'quizzes' => [
            'attempted' => 0,
        ]
    ];

    // Get assignments for this classroom
    $assignments = $assignmentModel->getAssignmentsForClassroom($classroomId);
    
    foreach ($assignments as $assignment) {
        $stats = $assignmentModel->getAssignmentStats($assignment['assignment_id'], $studentId);
        if ($stats) {
            $classroomStats[$classroomId]['assignments']['attempted']++;
            if ($stats['submitted_at']) {
                $classroomStats[$classroomId]['assignments']['submitted']++;
                if (strtotime($assignment['due_date']) < strtotime($stats['submitted_at'])) {
                    $classroomStats[$classroomId]['assignments']['lateSubmissions']++;
                }
            }
        }
    }

    // Get quizzes for this classroom
    $quizzes = $quizModel->getQuizForClassroom($classroomId);
    
    foreach ($quizzes as $quiz) {
        $stats = $quizModel->getQuizStats($quiz['quiz_id'], $studentId);
        if ($stats) {
            $classroomStats[$classroomId]['quizzes']['attempted']++;
        }
    }
}

// Prepare data for charts
$labels = [];
$assignmentData = [];
$quizData = [];

foreach ($classroomStats as $classroomId => $stats) {
    $labels[] = $stats['name']; // Add classroom name to labels
    $assignmentData[] = $stats['assignments']['attempted'];
    $assignmentData[] = $stats['assignments']['submitted'];
    $assignmentData[] = $stats['assignments']['lateSubmissions'];
    $quizData[] = $stats['quizzes']['attempted'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Progress Overview by Classroom</title>
    <link rel="stylesheet" href="../assets/css/progress.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            width: 300px;
            height: 300px;
            margin: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Personal Progress for <?php echo htmlspecialchars($student['username']); ?></h1>

    <div class="charts-container">
        <div class="chart-container">
            <h2>Assignments Progress by Classroom</h2>
            <canvas id="assignmentsChart"></canvas>
        </div>
        <div class="chart-container">
            <h2>Quizzes Attempted by Classroom</h2>
            <canvas id="quizzesChart"></canvas>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctxAssignments = document.getElementById('assignmentsChart').getContext('2d');
        const ctxQuizzes = document.getElementById('quizzesChart').getContext('2d');

        const labels = <?php echo json_encode($labels); ?>;
        const assignmentData = <?php echo json_encode($assignmentData); ?>;
        const quizData = <?php echo json_encode($quizData); ?>;

        // Assignments Chart
        new Chart(ctxAssignments, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Assignments Attempted',
                        data: assignmentData.filter((_, index) => index % 3 === 0), // Every 3rd for attempted
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    },
                    {
                        label: 'Assignments Submitted',
                        data: assignmentData.filter((_, index) => index % 3 === 1), // Every 3rd for submitted
                        backgroundColor: 'rgba(153, 102, 255, 0.6)',
                    },
                    {
                        label: 'Late Submissions',
                        data: assignmentData.filter((_, index) => index % 3 === 2), // Every 3rd for late submissions
                        backgroundColor: 'rgba(255, 159, 64, 0.6)',
                    },
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.dataset.label + ': ' + tooltipItem.raw;
                            }
                        }
                    }
                }
            }
        });

        // Quizzes Chart
        new Chart(ctxQuizzes, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Quizzes Attempted',
                    data: quizData,
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.dataset.label + ': ' + tooltipItem.raw;
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
