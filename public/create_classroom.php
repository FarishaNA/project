<?php
// Include necessary files
require_once '../config/database.php';
require_once '../models/Classroom.php';
require_once '../models/User.php';
include '../includes/dashboard_header.php'; 



// Ensure the user is logged in 
if (!isset($_SESSION['user_id'])) {
    header('Location: ../views/login.php');
    exit();
}

// Create an instance of the Classroom model
$classroomModel = new Classroom();

//create an instance of the user model
$user = new User();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    if(isset($_POST['teacher'])){
    $teacherId = $_POST['teacher'];

    if(is_numeric($teacherId)){
        if(!$user->getUserById($teacherId))

           $error = 'Teacher not found.';
    }
    else{

          $error = 'Teacher not found.';
    } 
}
  else{
     $teacherId=$_SESSION['user_id'];
  }
    $success = $classroomModel->createClassroom($teacherId, $name, $description);

    if ($success) {
        if($_SESSION['role']=="admin")
           
           header('Location: admin_dashboard.php');
        
        elseif($_SESSION['role']=="teacher")
         
          header('Location: teacher_dashboard.php');

    } else {
        $error = 'Failed to create classroom.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Classroom</title>
    <link rel="stylesheet" href="../assets/css/teacher_dashboard.css">
    <link rel="stylesheet" href="../assets/css/components/form.css">
    <link rel="stylesheet" href="../assets/css/form_center.css">
</head>
<body>
    <div class="container">
        <h1>Create New Classroom</h1>

        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form action="create_classroom.php" method="post">
            <label for="name">Classroom Name:</label>
            <input type="text" id="name" name="name" required><br>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" required></textarea><br>
            
            <?php if ($_SESSION['role'] == "admin"): ?>
                <label for="teacher">Teacher Id</label>
                <input type="text" id="teacher" name="teacher" required><br>
            <?php endif; ?>

            <button type="submit">Create Classroom</button>
        </form>
    </div>
</body>
</html>
