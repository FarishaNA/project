<?php
// Include necessary files and initialize session
include '../config/database.php';
include '../models/User.php';
include '../models/Feedback.php';
include '../models/Classroom.php';
include '../includes/back_button.php';

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$feedbackModel = new Feedback();
$userModel = new User();
$classroomModel = new Classroom();

if (isset($_GET['delete_feedback'])) {
    $feedback_id = $_GET['delete_feedback'];
    if ($feedbackModel->deleteFeedback($feedback_id)) {
        header("Location: feedback.php?deleted_success=true");
        exit();
    } else {
        header("Location: feedback.php?error=true");
        exit();
    }
}

// Fetch feedbacks received and sent by the user
$feedbacks = $feedbackModel->fetchFeedback($user_id);

$sent_feedbacks = [];
if ($role !== 'admin') {
    $sent_feedbacks = $feedbackModel->getUserfeedbacks($user_id);
}

// Get classrooms and teachers for the student
$classroomTeachers = [];
if ($role === 'student') {
    $classrooms = $classroomModel->getClassroomsForStudent($user_id);
    foreach ($classrooms as $classroom) {
        $classroomTeachers[$classroom['classroom_id']] = $classroom['teacher_id'];
    }
}

// Handle form submission for feedback
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_feedback'])) {
    $feedback_text = $_POST['feedback_text'];
    $recipient = null;
    $classroom_id = null;

    if ($role === 'teacher') {
        // Teachers submit feedback to the admin (ID 1)
        $recipient = 1;
        $feedbackId = $feedbackModel->insertFeedback($user_id, $feedback_text, $recipient, $classroom_id);
    } elseif ($role === 'student') {
        // Students can send feedback to admin or classroom teacher
        if ($_POST['feedback_recipient'] === 'admin') {
            $recipient = 1;
            $feedbackId = $feedbackModel->insertFeedback($user_id, $feedback_text, $recipient, $classroom_id);
        } else {
            // Sending feedback to classroom teacher
            $classroom_id = $_POST['classroom_id'];
            $classroom_teacher_id = $classroomTeachers[$classroom_id]; // Teacher ID is fetched from the classroom ID
            $feedbackModel->insertFeedback($user_id, $feedback_text, $classroom_teacher_id, $classroom_id);
        }
    }

    header("Location: feedback.php?success=true");
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feedback</title>
    <link rel="stylesheet" href="../assets/css/notifications.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        <?php if ($role === 'teacher'): ?>
            $('#received_notifications').show(); // Default to received for teachers
            $('#sent_notifications').hide();
            $('#send_notification_form').hide();
            $('#receivedFeedbackButton').addClass('active'); // Set active button

        <?php elseif ($role === 'student'): ?>
            $('#send_notification_form').show(); // Default to create feedback for students
            $('#sent_notifications').hide();
            $('#received_notifications').hide();
            $('#createFeedbackButton').addClass('active'); // Set active button

        <?php elseif ($role === 'admin'): ?>
            $('#received_notifications').show(); // Default to received for admins (only option)
            $('#sent_notifications').hide();
            $('#send_notification_form').hide();
        <?php endif; ?>
        
        // Toggle classroom selection based on the recipient option
        $('#feedback_recipient').change(function() {
            let selectedRecipient = $(this).val();
            if (selectedRecipient === 'teacher') {
                $('#teacher_selection').show();
                $('#classroom_select').attr('required', true); // Make classroom select required
            } else {
                $('#teacher_selection').hide();
                $('#classroom_select').removeAttr('required'); // Remove required when 'admin' is selected
            }
        });
        
        // Initial state: Hide classroom selection if the page is loaded with 'admin' selected
        if ($('#feedback_recipient').val() === 'admin') {
            $('#teacher_selection').hide();
            $('#classroom_select').removeAttr('required');
        }

        // Toggle feedback sections for teachers
        $('#receivedFeedbackButton').click(function() {
            $('#received_notifications').show();
            $('#sent_notifications').hide();
            $('#send_notification_form').hide();
            $('.navbuttons button').removeClass('active');
            $(this).addClass('active');
        });

        $('#sentFeedbackButton').click(function() {
            $('#received_notifications').hide();
            $('#sent_notifications').show();
            $('#send_notification_form').hide();
            $('.navbuttons button').removeClass('active');
            $(this).addClass('active');
        });

        $('#createFeedbackButton').click(function() {
            $('#received_notifications').hide();
            $('#sent_notifications').hide();
            $('#send_notification_form').show();
            $('.navbuttons button').removeClass('active');
            $(this).addClass('active');
        });
    });
</script>

</head>
<body>
<div class="notification-container">
    <div class="navbuttons">
    <?php if ($role === 'teacher'): ?>
    
            <button id="receivedFeedbackButton">Received Feedback</button>
            <button id="sentFeedbackButton">Sent Feedback</button>
            <button id="createFeedbackButton">Create Feedback</button>
        <?php elseif ($role === 'student'): ?>
            <button id="createFeedbackButton">Create Feedback</button>
            <button id="sentFeedbackButton">Sent Feedback</button>
    <?php endif; ?>
    </div>
    <?php if (isset($_GET['deleted_success'])): ?>
        <p class="success">Feedback deleted successfully!</p>
    <?php elseif (isset($_GET['error'])): ?>
        <p class="error">An error occurred. Please try again.</p>
    <?php elseif (isset($_GET['success'])): ?>
        <p class="success">Feedback submitted successfully!</p>
    <?php endif; ?>

    <?php if ($role === 'teacher'): ?>
        <div id="received_notifications">
        <!-- <div id="receivedFeedbackContainer"> -->
            <h3>Received Feedback</h3>
            <?php if (empty($feedbacks)): ?>
                <p>No feedback received.</p>
            <?php else: ?>
                <?php
                $groupedFeedback = [];
                foreach ($feedbacks as $feedback) {
                    $classroomName = $classroomModel->getClassroomNameById($feedback['classroom_id']);
                    $groupedFeedback[$classroomName][] = $feedback;
                }

                foreach ($groupedFeedback as $classroomName => $classroomFeedbacks) {
                    echo "<h4>Feedback for Classroom: " . htmlspecialchars($classroomName) . "</h4><ul>";
                    foreach ($classroomFeedbacks as $feedback) {
                        $username = $userModel->getUsernameById($feedback['user_id']);
                        echo "<li>
                            <strong>Feedback:</strong>
                            <p>" . htmlspecialchars($feedback['feedback_text']) . "</p>
                            <em>Submitted by User: " . htmlspecialchars($username) . " on " . htmlspecialchars($feedback['submitted_at']) . "</em>
                        </li>";
                    }
                    echo "</ul>";
                }
                ?>
            <?php endif; ?>
        </div>

        <div id="sent_notifications">
         <!-- <div id="sentFeedbackContainer" style="display: none;"> -->
            <h3>Sent Feedback</h3>
            <ul>
                <?php if (empty($sent_feedbacks)): ?>
                    <p>No feedback sent.</p>
                <?php else: ?>
                    <?php foreach ($sent_feedbacks as $feedback): ?>
                        <?php $username = $userModel->getUsernameById($feedback['receiver_id']); ?>
                        <li>
                            <strong>Feedback to: <?php echo htmlspecialchars($username); ?>:</strong>
                            <p><?php echo htmlspecialchars($feedback['feedback_text']); ?></p>
                            <em>Submitted on <?php echo htmlspecialchars($feedback['submitted_at']); ?></em>
                            <a href="feedback.php?delete_feedback=<?php echo $feedback['feedback_id']; ?>" 
                            onclick="return confirm('Are you sure you want to delete this feedback?');">Delete</a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>

        <div id="send_notification_form">
        <!-- <div id="createFeedbackContainer" style="display: none;"> -->
            <h3>Create Feedback</h3>
            <form method="POST">
                <label for="feedback_text">Feedback:</label>
                <textarea name="feedback_text" id="feedback_text" required></textarea>
                <button type="submit" name="submit_feedback">Submit Feedback</button>
            </form>
        </div>

    <?php elseif ($role === 'student'): ?>
        <div id="send_notification_form">
            <h2>Submit Feedback</h2>
            <form method="POST">
                <label for="feedback_text">Feedback:</label>
                <textarea name="feedback_text" id="feedback_text" required></textarea>

                <label for="feedback_recipient">Send To:</label>
                <select name="feedback_recipient" id="feedback_recipient" required>
                    <option value="admin">Admin</option>
                    <option value="teacher">Classroom Teacher</option>
                </select>

                <div id="teacher_selection" style="display: none;">
                    <label for="classroom_select">Select Classroom:</label>
                    <select name="classroom_id" id="classroom_select" required>
                        <option value="">Select a classroom</option>
                        <?php foreach ($classroomTeachers as $classroom_id => $teacher_id): ?>
                            <?php $classroomName = $classroomModel->getClassroomNameById($classroom_id); ?>
                            <option value="<?php echo htmlspecialchars($classroom_id); ?>">
                                Classroom: <?php echo htmlspecialchars($classroomName); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" name="submit_feedback" class="submit-button">Submit Feedback</button>
            </form>
        </div>

        <!-- Sent Feedbacks for Students -->
        <div id="sent_notifications">
            <h3>Sent Feedback</h3>
            <ul>
                <?php if (empty($sent_feedbacks)): ?>
                    <p>No feedback sent.</p>
                <?php else: ?>
                    <?php foreach ($sent_feedbacks as $feedback): ?>
                        <?php $recipientName = $userModel->getUsernameById($feedback['receiver_id']); ?>
                        <li>
                            <strong>Feedback to: <?php echo htmlspecialchars($recipientName); ?>:</strong>
                            <p><?php echo htmlspecialchars($feedback['feedback_text']); ?></p>
                            <em>Submitted on <?php echo htmlspecialchars($feedback['submitted_at']); ?></em>
                            <a href="feedback.php?delete_feedback=<?php echo $feedback['feedback_id']; ?>"
                            onclick="return confirm('Are you sure you want to delete this feedback?');">Delete</a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>

        
    <?php elseif ($role === 'admin'): ?>
        <div id="received_notifications">
            <h2>Received Feedback</h2>
            <ul>
                <?php 
                if (empty($feedbacks)) {
                    echo "<p>No feedback received.</p>";
                } else {
                    foreach ($feedbacks as $feedback) {
                        $username = $userModel->getUsernameById($feedback['user_id']);
                        
                        echo "<li>
                                <strong>Feedback:</strong>
                                <p>" . htmlspecialchars($feedback['feedback_text']) . "</p>
                                <em>
                                    <strong>Feedback from User : " . htmlspecialchars($username) . "</strong> on " . htmlspecialchars($feedback['submitted_at']) . "
                                </em>
                                <a href='?delete_feedback=" . $feedback['feedback_id'] . "' 
                                    onclick=\"return confirm('Are you sure you want to delete this feedback?');\">Delete</a>
                              </li>";
                    }                    
                }
                ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
