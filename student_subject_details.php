<?php
session_start();
include('db.php');

// Check if the user is logged in as a student or parent
if (!isset($_SESSION['student_logged_in']) && !isset($_SESSION['parent_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Get the subject from the URL
if (isset($_GET['subject'])) {
    $subject_name = $_GET['subject'];
} else {
    // Redirect if no subject is provided
    header("Location: subjects_view.php");
    exit();
}

// Initialize year and section variables
$year = null;
$section = null;

// Fetch the student's or parent's year and section
if (isset($_SESSION['student_logged_in'])) {
    $username = $_SESSION['student_username'];
    $query = $conn->prepare("SELECT year, section FROM students WHERE username = ?");
    $query->bind_param("s", $username);
    $query->execute();
    $result = $query->get_result();
    $user = $result->fetch_assoc();

    $year = $user['year'];
    $section = $user['section'];
} elseif (isset($_SESSION['parent_logged_in'])) {
    $username = $_SESSION['parent_username'];
    $query = $conn->prepare("SELECT lrn_students AS lrn FROM parents WHERE username = ?");
    $query->bind_param("s", $username);
    $query->execute();
    $result = $query->get_result();
    $user = $result->fetch_assoc();

    $lrn = $user['lrn'];
    // Fetch the year and section for the student associated with this parent
    $student_query = $conn->prepare("SELECT year, section FROM students WHERE lrn = ?");
    $student_query->bind_param("s", $lrn);
    $student_query->execute();
    $student_result = $student_query->get_result();
    
    if ($student_result->num_rows > 0) {
        $student_data = $student_result->fetch_assoc();
        $year = $student_data['year'];
        $section = $student_data['section'];
    } else {
        // Handle the case where no student is found
        echo "<p>No student found associated with this parent.</p>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title><?php echo htmlspecialchars($subject_name); ?> - Details</title>
</head>
<body>
    <h1><?php echo htmlspecialchars($subject_name); ?> - Options</h1>

    <h3>Available Options</h3>
    <ul>
        <li><a href="attendance_view.php?subject=<?php echo urlencode($subject_name); ?>">Attendance</a></li>
        <li><a href="grade_view.php?subject=<?php echo urlencode($subject_name); ?>">Grade</a></li>
    </ul>

    <h3>Actions</h3>
    <ul>
        <li><a href="subjects_view.php">Back to Subjects</a></li>
        <li><a href="dashboard.php">Back to Dashboard</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</body>
</html>
