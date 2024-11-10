<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Classroom</title>
    <!-- <link rel="stylesheet" href="../assets/css/join_classroom.css"> -->
    <link rel="stylesheet" href="../assets/css/components/form.css">
</head>
<body>
   ><div class="container"><!-- Changed class name here -->
        <h1>Join a Classroom</h1>

        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form action="join_classroom.php" method="post">
            <label for="classroom_id">Select Classroom:</label>
            <select id="classroom_id" name="classroom_id" required>
                <?php foreach ($classrooms as $classroom): ?>
                    <option value="<?php echo $classroom['classroom_id']; ?>">
                        <?php echo htmlspecialchars($classroom['classroom_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select><br>

            <button type="submit">Join Classroom</button>
        </form>
    </div>
</body>
</html>
