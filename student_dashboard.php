<?php
session_start();
include('db.php');

// Check if the student is logged in
if (!isset($_SESSION['student_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Fetch student details
$username = $_SESSION['student_username'];
$query = $conn->prepare("SELECT * FROM students WHERE username = ?");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();
$student = $result->fetch_assoc();

if ($student) {
    $lrn = $student['lrn'];
    $year = $student['year'];
    $section = $student['section'];
} else {
    echo "Student not found.";
    exit();
}

// Fetch all available year levels from the database
$year_levels_query = "SELECT DISTINCT year_level FROM subjects_sections ORDER BY year_level ASC";
$year_levels_result = mysqli_query($conn, $year_levels_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">
    <title>Student Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($student['first_name']); ?>!</h1>

    <h2>Your Information</h2>
    <p><strong>LRN:</strong> <?php echo htmlspecialchars($student['lrn']); ?></p>
    <p><strong>Full Name:</strong> <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
    <p><strong>Year Level:</strong> <?php echo htmlspecialchars($student['year']); ?></p>
    <p><strong>Section:</strong> <?php echo htmlspecialchars($student['section']); ?></p>

    <h2>Your Year Levels</h2>
    
    <?php if (mysqli_num_rows($year_levels_result) > 0): ?>
        <ul>
            <?php while ($year_level = mysqli_fetch_assoc($year_levels_result)): ?>
                <li>
                    <a href="subjects_view.php?year_level=<?php echo urlencode($year_level['year_level']); ?>">
                        <?php echo "Year " . htmlspecialchars($year_level['year_level']); ?> View
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No year levels available.</p>
    <?php endif; ?>
    
    <!-- Announcements Section -->
    <h2>Announcements</h2>
    <?php
    $announcements_query = "SELECT * FROM announcements WHERE audience IN ('students', 'all') ORDER BY date_posted DESC";
    $announcements_result = mysqli_query($conn, $announcements_query);
    if (mysqli_num_rows($announcements_result) > 0): ?>
        <ul>
            <?php while ($announcement = mysqli_fetch_assoc($announcements_result)): ?>
                <li>
                    <h3><?php echo htmlspecialchars($announcement['title']); ?></h3>
                    <p><?php echo htmlspecialchars($announcement['content']); ?></p>
                    <small>Posted on: <?php echo date('F j, Y', strtotime($announcement['date_posted'])); ?></small>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No announcements at the moment.</p>
    <?php endif; ?>

    <h2>Feedback</h2>
    <ul>
    <li><a href="feedback_view.php">View Feedback</a></li>
    </ul>

    <h2>Actions</h2>
    <ul>
        <li><a href="change_password.php">Change Password</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</body>
</html>
