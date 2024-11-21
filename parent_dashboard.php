<?php
session_start();
include('db.php');

// Check if the parent is logged in
if (!isset($_SESSION['parent_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Fetch parent details
$username = $_SESSION['parent_username'];
$query = $conn->prepare("SELECT * FROM parents WHERE username = ?");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();
$parent = $result->fetch_assoc();

if ($parent) {
    $lrn = $parent['lrn_students'];
} else {
    echo "Parent not found.";
    exit();
}

// Fetch student's details linked to the parent
$query = $conn->prepare("SELECT * FROM students WHERE lrn = ?");
$query->bind_param("s", $lrn);
$query->execute();
$result = $query->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    echo "Student not found for this parent.";
    exit();
}

// Fetch year levels for the student
$year_levels_query = "SELECT DISTINCT year_level FROM subjects_sections ORDER BY year_level ASC";
$year_levels_result = mysqli_query($conn, $year_levels_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">
    <title>Parent Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($parent['first_name']); ?>!</h1>

    <h2>Your Information</h2>
    <p><strong>Student LRN:</strong> <?php echo htmlspecialchars($lrn); ?></p>
    <p><strong>Full Name:</strong> <?php echo htmlspecialchars($parent['first_name'] . ' ' . $parent['last_name']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($parent['email']); ?></p>

    <h2>Your Student Year Levels</h2>
    
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
        <p>No year levels available for your child.</p>
    <?php endif; ?>

    <!-- Announcements Section -->
    <h2>Announcements</h2>
    <?php
    $announcements_query = "SELECT * FROM announcements WHERE audience IN ('parents', 'all') ORDER BY date_posted DESC";
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
