<?php
// Include necessary files and initialize session
include '../config/database.php';
include '../models/User.php';
include '../models/Notification.php';
include '../models/Classroom.php';
include '../includes/back_button.php';

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$userModel = new User();
$notificationModel = new Notification();
$classroomModel = new Classroom();

// Mark notification as read if requested
if (isset($_GET['mark_read'])) {
    $notification_id = $_GET['mark_read'];
    $notificationModel->markAsRead($notification_id, $user_id);
    header("Location: notifications.php");
    exit();
}

// Delete notification if requested
if (isset($_GET['delete_notification'])) {
    $notification_id = $_GET['delete_notification'];
    $notificationModel->deleteNotification($notification_id, $user_id);
    header("Location: notifications.php?deleted_success=true");
    exit();
}

// Fetch user's notifications
$notifications = $notificationModel->fetchNotifications($user_id);
$unread_count = $notificationModel->countUnreadNotifications($user_id);

// Fetch sent notifications (for non-student roles)
$sent_notifications = [];
if ($role !== 'student') {
    $sent_notifications = $notificationModel->getUserSentNotifications($user_id);
}

// Handle form submission to send new notification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_notification'])) {
    $message = $_POST['message'];
    $recipient = $_POST['recipient']; // "all", "classroom" or specific user IDs
    
    // Send notification
    $notificationId = $notificationModel->insertNotification($user_id, $message);

    if ($recipient === 'all') {
        // Get all users (excluding sender)
        $all_user_ids = $userModel->getAllUserIdsExcept($user_id);
        $notificationModel->insertNotificationRecipients($notificationId, $all_user_ids, $user_id);
    } elseif ($recipient === 'classroom') {
        // Send to a classroom (classroom ID passed from the form)
        $classroom_id = $_POST['classroom_id'];
        $classroom_users = $classroomModel->getStudentsForNotification($classroom_id);
        $notificationModel->insertNotificationRecipients($notificationId, $classroom_users, $user_id);
    } else {
        // Ensure specific users are selected
        if (!empty($_POST['specific_recipients'])) {
            $specific_recipients = $_POST['specific_recipients'];
            $notificationModel->insertNotificationRecipients($notificationId, $specific_recipients, $user_id);
        } else {
            // Handle error (no specific recipients selected)
            header("Location: notifications.php?error=no_recipients");
            exit();
        }
    }
    
    header("Location: notifications.php?sent_success=true");
    exit();
}

// Fetch all users and classrooms for admin/teacher roles
$all_users = [];
$classrooms = [];
$all_users = $userModel->getAllUsers();
if ($role === 'admin') {
    $classrooms = $classroomModel->getAllClassrooms();
} elseif ($role === 'teacher') {
    $classrooms = $classroomModel->getAllClassroomsForTeacher($user_id);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Notifications</title>
    <link rel="stylesheet" href="../assets/css/notifications.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Show received notifications by default
            $('#received_notifications').show();
            $('#sent_notifications').hide();
            $('#send_notification_form').hide();
            
            // Mark the first button as active
            $('#show_received_notifications').addClass('active');

            // Handle tab switching and button color change
            $('#show_received_notifications').click(function() {
                $('#received_notifications').show();
                $('#sent_notifications').hide();
                $('#send_notification_form').hide();
                
                // Change button colors
                $('.navbuttons button').removeClass('active');
                $(this).addClass('active');
            });

            $('#show_sent_notifications').click(function() {
                $('#received_notifications').hide();
                $('#sent_notifications').show();
                $('#send_notification_form').hide();
                
                // Change button colors
                $('.navbuttons button').removeClass('active');
                $(this).addClass('active');
            });

            $('#show_send_notification').click(function() {
                $('#received_notifications').hide();
                $('#sent_notifications').hide();
                $('#send_notification_form').show();
                
                // Change button colors
                $('.navbuttons button').removeClass('active');
                $(this).addClass('active');
            });

            $('#classroom_select').hide();
            $('#specific_recipients_container').hide();

            // Handle recipient type selection change
            $('#recipient').change(function() {
                let selectedValue = $(this).val();

                // If 'all' is selected, hide everything else
                if (selectedValue === 'all') {
                    $('#classroom_select').hide();
                    $('#specific_recipients_container').hide();
                    $('#specific_recipients input').prop('checked', false);
                    $('#specific_recipients').prop('disabled', true);
                }

                // If 'classroom' is selected, show classroom select and hide specific users
                else if (selectedValue === 'classroom') {
                    $('#classroom_select').show();
                    $('#specific_recipients_container').hide();
                    $('#specific_recipients input').prop('checked', false);
                    $('#specific_recipients').prop('disabled', true);
                }

                // If 'specific' is selected, show specific users and hide classroom select
                else if (selectedValue === 'specific') {
                    $('#classroom_select').hide();
                    $('#specific_recipients_container').show();
                    $('#specific_recipients').prop('disabled', false);
                }
            });
        });
    </script>
</head>
<body>
 <div class="notification-container">
    <div class="navbuttons">
        <!-- Navigation buttons -->
        <?php if ($role !== 'student'): ?>    
            <button id="show_received_notifications">Received Notifications</button>
            <button id="show_sent_notifications">Sent Notifications</button>
            <button id="show_send_notification">Create Notification</button>
        <?php endif; ?>
    </div>

    <!-- Received Notifications -->
    <div id="received_notifications">
        <?php if (count($notifications) > 0): ?>
            <h2>Your Notifications</h2>
            <ul>
                <?php foreach ($notifications as $notification): ?>
                    <li class="<?php echo $notification['is_read'] ? 'read' : 'unread'; ?>">
                        <strong><?php echo htmlspecialchars($notification['message']); ?></strong>
                        <em><?php echo htmlspecialchars($notification['created_at']); ?></em>
                        <?php if (!$notification['is_read']): ?>
                            <a href="?mark_read=<?php echo $notification['id']; ?>">Mark as Read</a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No notifications received.</p>
        <?php endif; ?>
    </div>

    <!-- Sent Notifications -->
    <div id="sent_notifications">
        <?php if (count($sent_notifications) > 0): ?>
            <h2>Your Sent Notifications</h2>
            <ul>
                <?php foreach ($sent_notifications as $sent): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($sent['message']); ?></strong>
                        <em><?php echo htmlspecialchars($sent['created_at']); ?></em>
                        <a href="?delete_notification=<?php echo $sent['id']; ?>">Delete</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>You have not sent any notifications.</p>
        <?php endif; ?>
    </div>

    <!-- Send Notification Form -->
    <div id="send_notification_form">
        <h2>Send New Notification</h2>
        <?php if (isset($_GET['sent_success'])): ?>
            <p class="success">Notification sent successfully!</p>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <p class="error">Please select at least one specific recipient!</p>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="message">Message:</label>
            <textarea name="message" id="message" required></textarea>

            <label for="recipient">Send To:</label>
            <select name="recipient" id="recipient" required>
                <option value="all">All Users</option>
                <?php if (!empty($classrooms)): ?>
                    <option value="classroom">Classroom</option>
                <?php endif; ?>
                <option value="specific">Specific Users</option>
            </select>

            <!-- Classroom select (visible when "Classroom" is chosen) -->
            <div id="classroom_select" style="display:none;">
                <label for="classroom_id">Select Classroom:</label>
                <select name="classroom_id" id="classroom_id">
                    <?php foreach ($classrooms as $classroom): ?>
                        <option value="<?php echo $classroom['classroom_id']; ?>">
                            <?php echo htmlspecialchars($classroom['classroom_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Specific recipients (visible when "Specific" is chosen) -->
            <div id="specific_recipients_container" style="display:none;">
                <h4>Select Specific Users:</h4>
                <div id="specific_recipients">
                    <?php foreach ($all_users as $user): ?>
                        <label>
                            <input type="checkbox" name="specific_recipients[]" value="<?php echo $user['user_id']; ?>">
                            <?php echo htmlspecialchars($user['username']); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" name="send_notification" class="submit-button">Send Notification</button>
        </form>
    </div>
</div>
</body>
</html>
