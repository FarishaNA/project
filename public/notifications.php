<?php
// Include necessary files and initialize session
include '../config/database.php';
include '../models/User.php';
include '../models/Notification.php';
include '../includes/back_button.php';

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$userModel = new User();
$notificationModel = new Notification();

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

// Fetch user's notifications (viewable by all)
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
    $recipient = $_POST['recipient']; // "all" or selected user IDs
    
    // Send notification
    $notificationId = $notificationModel->insertNotification($user_id, $message);

    if ($recipient === 'all') {
        // Get all users (excluding sender)
        $all_user_ids = $userModel->getAllUserIdsExcept($user_id);
        $notificationModel->insertNotificationRecipients($notificationId, $all_user_ids);
    } else {
        // Ensure at least one user is selected
        if (!empty($_POST['specific_recipients'])) {
            $specific_recipients = $_POST['specific_recipients'];
            $notificationModel->insertNotificationRecipients($notificationId, $specific_recipients);
        } else {
            // Handle error (no specific recipients selected)
            header("Location: notifications.php?error=no_recipients");
            exit();
        }
    }
    
    header("Location: notifications.php?sent_success=true");
    exit();
}

// Fetch all users (for user selection in the form)
$all_users = [];
if ($role !== 'student') {
    $all_users = $userModel->getAllUsers();
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

            // Handle button clicks
            $('#show_received_notifications').click(function() {
                $('#received_notifications').show();
                $('#sent_notifications').hide();
                $('#send_notification_form').hide();
            });

            $('#show_sent_notifications').click(function() {
                $('#received_notifications').hide();
                $('#sent_notifications').show();
                $('#send_notification_form').hide();
            });

            $('#show_send_notification').click(function() {
                $('#received_notifications').hide();
                $('#sent_notifications').hide();
                $('#send_notification_form').show();
            });

            // Handle recipient selection
            $('#recipient').change(function() {
                if ($(this).val() === 'all') {
                    $('#specific_recipients').prop('disabled', true);
                } else {
                    $('#specific_recipients').prop('disabled', false);
                }
            });
        });
    </script>
</head>
<body>

    <div class="notification-container">
        <h2>User Notifications</h2>

        <!-- Navigation buttons -->
        <button id="show_received_notifications">Received Notifications</button>
        <button id="show_sent_notifications">Sent Notifications</button>
        <button id="show_send_notification">Send Notification</button>

        <!-- Received Notifications -->
        <div id="received_notifications">
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
        </div>

        <!-- Sent Notifications -->
        <div id="sent_notifications">
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
            <?php if (isset($_GET['deleted_success'])): ?>
                <p class="success">Notification deleted successfully!</p>
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
                    <option value="specific">Specific Users</option>
                </select>

                <label for="specific_recipients">Select Specific Users:</label>
                <select name="specific_recipients[]" id="specific_recipients" multiple disabled>
                    <?php foreach ($all_users as $user): ?>
                        <option value="<?php echo $user['user_id']; ?>">
                            <?php echo htmlspecialchars($user['username']); ?> (<?php echo htmlspecialchars($user['role']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" name="send_notification">Send Notification</button>
            </form>
        </div>
    </div>

</body>
</html>
