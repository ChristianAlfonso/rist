<?php
session_start();
// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Sample Grade Levels
$grades = [7, 8, 9, 10];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>School Management System</title>
</head>
<body>
    <h1>Welcome to the School Management System</h1>
    <?php foreach ($grades as $grade): ?>
        <h2>Grade <?php echo $grade; ?></h2>
        <ul>
            <?php
            // Fetch subjects for this grade from the database
            $query = "SELECT * FROM subjects WHERE grade_level = $grade";
            $result = mysqli_query($conn, $query);
            while ($subject = mysqli_fetch_assoc($result)):
            ?>
                <li>
                    <a href="subjects/<?php echo strtolower($subject['subject_name']); ?>.php">
                        <?php echo $subject['subject_name']; ?>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php endforeach; ?>
</body>
</html>
