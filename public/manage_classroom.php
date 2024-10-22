<?php
require_once '../config/database.php';
require_once '../models/Classroom.php';
require '../includes/back_button.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../views/login.php');
    exit();
}

$classroomModel = new Classroom();
$userId = $_SESSION['user_id'];
$role = $_SESSION['role'];
$classroomId = isset($_GET['id']) ? $_GET['id'] : null;
$action = isset($_GET['action']) ? $_GET['action'] : null;

// Leaving the classroom
if ($action === 'leave' && $classroomId) {
    $classroomModel->removeStudentFromClassroom($userId, $classroomId);
    echo "<script>
            alert('You have left the classroom.');
            window.location.href = 'student_dashboard.php'; 
    </script>";
}

// Deleting the classroom (for teachers)
if ($action === 'delete' && $role === 'teacher' && $classroomId) {
    $classroomModel->deleteClassroom($classroomId);
    echo "<script>
            alert('Classroom deleted successfully.');
            window.location.href = 'teacher_dashboard.php';
    </script>";
   
}

// Updating the classroom (for teachers)
if ($action === 'update' && $role === 'teacher' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $newName = $_POST['name'];
    $newDescription = $_POST['description'];

    // Call update function with the new name and description
    $classroomModel->updateClassroom($classroomId, $newName, $newDescription);

    echo "<script>
            alert('Classroom updated successfully.');
            window.location.href = 'teacher_dashboard.php';
        </script>";
    exit(); 
}

// Fetch classroom details if updating
$classroomDetails = ($action === 'update' && $classroomId) ? $classroomModel->getClassroomById($classroomId) : null;
?>

<!-- Update Classroom Form -->
<?php if ($action === 'update' && $role === 'teacher'): ?>
    <link rel="stylesheet" href="../assets/css/components/form.css">
    <div class="container">
    <form method="POST" action="manage_classroom.php?id=<?php echo $classroomId; ?>&action=update">
        <label for="name">Classroom Name:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($classroomDetails['classroom_name']); ?>" required>
        
        <label for="description">Classroom Description:</label>
        <textarea id="description" name="description" required><?php echo htmlspecialchars($classroomDetails['description']); ?></textarea>
        
        <button type="submit">Update Classroom</button>
    </form>
    </div>
<?php endif; ?>

