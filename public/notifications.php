<?php
// notifications.php
include '../includes/back_button.php';
require '../config/database.php';
require '../models/Notification.php';
require '../models/User.php';


$userModel = new User();
$notificationModel = new Notification();
$userId = $_SESSION['user_id'];

// Handle sending notification (for admin/teacher)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_notification'])) {
    $message = $_POST['message'];
    $sendToAll = isset($_POST['send_to_all']) ? true : false;

    $recipientIds = [];
    if (!$sendToAll) {
        $recipientIds = $_POST['recipient_ids'];
    } else {
        // If sending to all, fetch all user IDs
        $result = $userModel->getAllUsers();
        while ($row = mysqli_fetch_assoc($result)) {
            $recipientIds[] = $row['user_id'];
        }
    }

    $notificationId = $notificationModel->insertNotification($userId, $message);
    $notificationModel->insertNotificationRecipients($notificationId, $recipientIds);
    $success_msg = "Notification sent successfully!";
}

// Handle marking a notification as read
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mark_as_read'])) {
    $notificationId = $_POST['notification_id'];
    $notificationModel->markAsRead($notificationId, $userId);
    echo $notificationModel->countUnreadNotifications($userId);
    exit();
}

// Fetch all notifications for the user
$notifications = $notificationModel->fetchNotifications($userId);
$unreadCount = $notificationModel->countUnreadNotifications($userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="../assets/css/notifications.css">
    <script>
        function markAsRead(notificationId) {
            // Send an AJAX request to mark the notification as read
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '', true); // current file
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('notification-' + notificationId).classList.add('read');
                    document.getElementById('unread-count').innerText = xhr.responseText;
                }
            };
            xhr.send('mark_as_read=1&notification_id=' + notificationId);
        }
    </script>
</head>
<body>

<div class="container">
    

    <h2>Notifications</h2>

    <!-- Notifications List -->
    <ul class="notifications-list">
        <?php while ($notification = mysqli_fetch_assoc($notifications)): ?>
            <li id="notification-<?php echo $notification['id']; ?>" 
                class="notification-item <?php echo $notification['is_read'] ? 'read' : 'unread'; ?>">
                <div class="notification-content">
                    <p><?php echo htmlspecialchars($notification['message']); ?></p>
                    <small>Sent at: <?php echo $notification['created_at']; ?></small>
                </div>
                <?php if (!$notification['is_read']): ?>
                    <button class="mark-read-btn" onclick="markAsRead(<?php echo $notification['id']; ?>)">Mark as Read</button>
                <?php endif; ?>
            </li>
        <?php endwhile; ?>
    </ul>

    <!-- Send Notification Form for Admin/Teacher -->
    <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'teacher'): ?>
        <form action="" method="POST" class="notification-form">
            <textarea name="message" placeholder="Enter your notification message" required></textarea>
            <label>
                <input type="checkbox" name="send_to_all" value="1"> Send to All Users
            </label>
            <label for="recipients">Select Recipients:</label>
            <select id="recipient_ids" name="recipient_ids[]" multiple>
                <?php
                $users = $userModel->getAllUsers(); // Fetch all users
                if (!empty($users)) {
                    foreach ($users as $user) {
                        echo "<option value='{$user['user_id']}'>{$user['username']} ({$user['role']})</option>";
                    }
                } else {
                    echo "<option>No users found</option>";
                }
                ?>
            </select>
            <button type="submit" name="send_notification">Send Notification</button>
        </form>
        <p class="success-msg"><?php if (isset($success_msg)) echo $success_msg; ?></p>
    <?php endif; ?>
</div>

</body>
</html>
