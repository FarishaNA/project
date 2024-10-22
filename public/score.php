<?php 
include '../includes/back_button.php';
// Retrieve the score and total questions from the query parameters
$totalScore = isset($_GET['score']) ? intval($_GET['score']) : 0;
$totalQuestions = isset($_GET['total']) ? intval($_GET['total']) : 0;
$classroomId = isset($_GET['classroom']) ? intval($_GET['classroom']) : 0;

if ($totalQuestions === 0) {
    echo 'Invalid score data.';
    exit();
}

$scorePercentage = ($totalScore / $totalQuestions) * 100;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results</title>
    <link rel="stylesheet" href="../assets/css/score.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <h1>Quiz Results</h1>
        <div id="score">
            <h2>Your Score: <?php echo $totalScore . '/' . $totalQuestions; ?> (<?php echo round($scorePercentage, 2); ?>%)</h2>
            <canvas id="scoreChart" width="400" height="200"></canvas>
            <a href="view_quizzes.php?classroom=<?php echo $_SESSION['selected_classroom']; ?>" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Quiz List
            </a>
        </div>
    </div>

    <script>
        var ctx = document.getElementById('scoreChart').getContext('2d');
        var scoreChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Correct', 'Incorrect'],
                datasets: [{
                    label: 'Score Distribution',
                    data: [<?php echo $totalScore; ?>, <?php echo $totalQuestions - $totalScore; ?>],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(255, 99, 132, 0.2)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
