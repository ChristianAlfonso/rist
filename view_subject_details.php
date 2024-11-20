<?php
session_start();
include('db.php');

// Check if the teacher is logged in
if (!isset($_SESSION['teacher_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Get the year, section, and subject from the URL parameters
$year = $_GET['year'] ?? '';
$section = $_GET['section'] ?? '';
$subject = $_GET['subject'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>View Actions for <?php echo htmlspecialchars($subject); ?></title>
</head>
<body>
    <h1>Actions for <?php echo htmlspecialchars($year . ' ' . $section . ': ' . $subject); ?></h1>
    
    <h2>Select an Action</h2>
    <ul>
        <li><a href="attendance.php?year=<?php echo urlencode($year); ?>&section=<?php echo urlencode($section); ?>&subject=<?php echo urlencode($subject); ?>">Attendance</a></li>
        <li><a href="grade.php?year=<?php echo urlencode($year); ?>&section=<?php echo urlencode($section); ?>&subject=<?php echo urlencode($subject); ?>">Grade</a></li>
    </ul>

    <a href="teacher_dashboard.php">Back to Dashboard</a>
</body>
</html>