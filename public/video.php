<?php
session_start();
require_once '../config/database.php';

$classroomId = $_SESSION['selected_classroom'] ?? '';
$userRole = $_SESSION['role'] ?? ''; // Assuming 'teacher' or 'student'
$userId = $_SESSION['user_id'] ?? ''; // Assuming this is available in the session

// Check if a conference is ongoing for the classroom
$query = "SELECT * FROM video_sessions WHERE classroom_id = $classroomId AND teacher_id = $userId";
$result = mysqli_query($conn, $query);
$session = mysqli_fetch_assoc($result);

$isActive = $session['is_active'] ?? 0; // Assuming 0 for inactive and 1 for active

if ($userRole == 'teacher') {
    echo '<button id="startConference">Start Video Conference</button>';
} elseif ($userRole == 'student') {
    if ($isActive) {
        echo '<button id="joinConference">Join Video Conference</button>';
    } else {
        echo '<p>No active video conference. Please wait for the teacher to start.</p>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Conference</title>
</head>
<body>
    <script src="https://meet.jit.si/external_api.js"></script>
    <script>
        document.getElementById('startConference')?.addEventListener('click', function() {
            const roomName = 'RoomName_' + '<?php echo $classroomId; ?>'; // Unique room name per classroom
            startVideo(roomName);

            // Update the database to set the conference as active
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_conference_status.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    console.log(xhr.responseText); // Log the server response
                } else {
                    console.error('Error:', xhr.statusText);
                }
            };
            xhr.send('classroom_id=' + encodeURIComponent('<?php echo $classroomId; ?>') + '&is_active=1');
        });

        document.getElementById('joinConference')?.addEventListener('click', function() {
            const roomName = 'RoomName_' + '<?php echo $classroomId; ?>';
            startVideo(roomName);
        });

        function startVideo(roomName) {
            const domain = 'meet.jit.si';
            const options = {
                roomName: roomName,
                parentNode: document.body,
                width: '100%',
                height: 600,
            };
            new JitsiMeetExternalAPI(domain, options);
        }
    </script>
</body>
</html>
