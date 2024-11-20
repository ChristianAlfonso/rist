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

// Handle form submission for adding a subject/section
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_name = $_POST['subject_name'];
    $year_level = $_POST['year_level'];
    $section = $_POST['section'];

    $insert_query = $conn->prepare("INSERT INTO subjects_sections (subject_name, year_level, section, teacher_id) VALUES (?, ?, ?, ?)");
    $insert_query->bind_param("ssss", $subject_name, $year_level, $section, $teacher_id);
    $insert_query->execute();
}

// Handle deletion of a subject/section
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_query = $conn->prepare("DELETE FROM subjects_sections WHERE id = ? AND teacher_id = ?");
    $delete_query->bind_param("ss", $delete_id, $teacher_id);
    $delete_query->execute();
}

// Fetch subjects and sections added by this teacher
$subjects_query = $conn->prepare("SELECT id, subject_name, year_level, section FROM subjects_sections WHERE teacher_id = ?");
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
    <title>Add Subject/Section</title>
</head>
<body>
    <h1>Add Subject/Section</h1>

    <form method="POST" action="">
        <label for="subject_name">Subject Name:</label>
        <input type="text" id="subject_name" name="subject_name" required>
        
        <label for="year_level">Year Level:</label>
        <input type="text" id="year_level" name="year_level" required>
        
        <label for="section">Section:</label>
        <input type="text" id="section" name="section" required>
        
        <button type="submit">Add Subject/Section</button>
    </form>

    <h2>Your Added Subjects and Sections</h2>
    <?php if ($subjects_result->num_rows > 0): ?>
        <ul>
            <?php while ($subject = $subjects_result->fetch_assoc()): ?>
                <li>
                    <?php echo htmlspecialchars($subject['year_level']) . " " . htmlspecialchars($subject['section']) . ": " . htmlspecialchars($subject['subject_name']); ?> 
                    <a href="?delete_id=<?php echo urlencode($subject['id']); ?>" onclick="return confirm('Are you sure you want to delete this subject/section?');">Delete</a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No subjects added yet.</p>
    <?php endif; ?>

    <h3>Actions</h3>
    <ul>
        <li><a href="teacher_dashboard.php">Back to Dashboard</a></li>
        <li><a href="change_password.php">Change Password</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</body>
</html>
