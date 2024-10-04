<?php
require_once '../config/database.php';

class Notification {

    private $db;

    public function __construct() {
        global $conn;
        $this->db = $conn;
    }

    // Insert notification into notifications table
    public function insertNotification($senderId, $message) {
        $query = "INSERT INTO notifications (sender_id, message) VALUES ('$senderId', '$message')";
        mysqli_query($this->db, $query);
        return mysqli_insert_id($this->db);
    }

    // Insert recipients into notification_recipients table
    public function insertNotificationRecipients($notificationId, $recipientIds) {
        foreach ($recipientIds as $recipientId) {
            $query = "INSERT INTO notification_recipients (notification_id, recipient_id, is_read) VALUES ('$notificationId', '$recipientId', 0)";
            mysqli_query($this->db, $query);
        }
    }

    // Fetch notifications for a specific user
    public function fetchNotifications($userId) {
        $query = "SELECT n.*, nr.is_read FROM notifications n 
                  JOIN notification_recipients nr ON n.id = nr.notification_id 
                  WHERE nr.recipient_id = '$userId' 
                  ORDER BY n.created_at DESC";
        return mysqli_query($this->db, $query);
    }

    // Mark a notification as read
    public function markAsRead($notificationId, $userId) {
        $query = "UPDATE notification_recipients SET is_read = 1 WHERE notification_id = '$notificationId' AND recipient_id = '$userId'";
        mysqli_query($this->db, $query);
    }

    // Count unread notifications for a specific user
    public function countUnreadNotifications($userId) {
        $query = "SELECT COUNT(*) as unread_count FROM notification_recipients WHERE recipient_id = '$userId' AND is_read = 0";
        $result = mysqli_query($this->db, $query);
        $row = mysqli_fetch_assoc($result);
        return $row['unread_count'];
    }
}
?>
