<?php
session_start();
include('db.php');

// Check if the teacher is logged in
if (!isset($_SESSION['teacher_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Fetch the logged-in teacher's information
$username = $_SESSION['teacher_username'];
$query = $conn->prepare("SELECT id, subject, first_name, last_name, email FROM teachers WHERE username = ?");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();
$teacher = $result->fetch_assoc();
$teacher_id = $teacher['id']; // Get the teacher's ID

// Fetch announcements
$announcement_query = $conn->prepare("SELECT * FROM announcements ORDER BY date_posted DESC");
$announcement_query->execute();
$announcements_result = $announcement_query->get_result();

// Fetch subjects and sections added by this teacher
$subjects_query = $conn->prepare("SELECT subject_name, year_level, section FROM subjects_sections WHERE teacher_id = ?");
$subjects_query->bind_param("s", $teacher_id);
$subjects_query->execute();
$subjects_result = $subjects_query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Teacher Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($teacher['first_name']); ?>!</h1>
    
    <h2>Your Information</h2>
    <p><strong>Teacher ID:</strong> <?php echo htmlspecialchars($teacher['id']); ?></p>
    <p><strong>Subject:</strong> <?php echo htmlspecialchars($teacher['subject']); ?></p>
    <p><strong>Full Name:</strong> <?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($teacher['email']); ?></p>

    <!-- Announcements Section -->
    <h2>Announcements</h2>
    <?php if (mysqli_num_rows($announcements_result) > 0): ?>
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

    <!-- Added Subjects and Sections -->
    <h2>Your Added Subjects and Sections</h2>
    <?php if ($subjects_result->num_rows > 0): ?>
        <ul>
            <?php while ($subject = $subjects_result->fetch_assoc()): ?>
                <li>
                    <?php echo htmlspecialchars($subject['year_level']) . " " . htmlspecialchars($subject['section']) . ": " . htmlspecialchars($subject['subject_name']); ?> 
                    <a href="view_subject_details.php?year=<?php echo urlencode($subject['year_level']); ?>&section=<?php echo urlencode($subject['section']); ?>&subject=<?php echo urlencode($subject['subject_name']); ?>">(View)</a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No subjects added yet.</p>
    <?php endif; ?>
    
    <h3>Send Feedback</h3>
    <ul>
    <li><a href="feedback_submit.php">Send Feedback</a></li>

    <h3>Actions</h3>
    <ul>
        <li><a href="add_subject_section.php">Add Subject/Section</a></li>

        <li><a href="change_password.php">Change Password</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</body>
</html>
