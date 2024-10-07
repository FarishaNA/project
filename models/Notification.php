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
        
        $result = mysqli_query($this->db, $query);
        
        if (!$result) {
        
            echo "Error: " . mysqli_error($this->db);
            return []; 
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }


    // Mark a notification as read
    public function markAsRead($notificationId, $userId) {
        $query = "UPDATE notification_recipients SET is_read = 1 WHERE notification_id = '$notificationId' AND recipient_id = '$userId'";
        mysqli_query($this->db, $query);
    }

    public function countUnreadNotifications($userId) {
        $query = "SELECT COUNT(*) as unread_count FROM notification_recipients WHERE recipient_id = '$userId' AND is_read = 0";
        $result = mysqli_query($this->db, $query);
    
        if ($result) {
          
            $row = mysqli_fetch_assoc($result);
            return (int)$row['unread_count']; // Cast to int to ensure the return type is correct
        } else {
            // Handle the error (optional)
            return 0; // Or throw an exception, or log the error, etc.
        }
    }

    // Fetch current user's sent notifications
    public function getUserSentNotifications($user_id) {
        $stmt = $this->db->prepare("
            SELECT n.message, n.created_at 
            FROM notifications AS n 
            WHERE n.sender_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }


    // Delete notification function
    public function deleteNotification($notification_id, $user_id) {
        // Ensure that the notification belongs to the user
        $query = "DELETE FROM notifications WHERE id = $notification_id AND sender_id = $user_id";
        if (mysqli_query($this->db, $query)) {
            return true; // Notification deleted successfully
        } else {
            return false; // Error occurred
        }
    }
}
?>
