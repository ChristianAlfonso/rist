<?php
session_start();
include('db.php');

// Check if the user is logged in as a student or parent
if (!isset($_SESSION['student_logged_in']) && !isset($_SESSION['parent_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Initialize year and section variables
$year = null;
$section = null;

// Fetch the student's or parent's LRN
if (isset($_SESSION['student_logged_in'])) {
    $username = $_SESSION['student_username'];
    $query = $conn->prepare("SELECT year, section FROM students WHERE username = ?");
} elseif (isset($_SESSION['parent_logged_in'])) {
    $username = $_SESSION['parent_username'];
    $query = $conn->prepare("SELECT lrn_students AS lrn FROM parents WHERE username = ?");
}

$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

// For students, get year and section
if (isset($_SESSION['student_logged_in'])) {
    $year = $user['year'];
    $section = $user['section'];

    // Fetch subjects for this student's year and section
    $subject_query = $conn->prepare("SELECT subject_name FROM subjects_sections WHERE year_level = ? AND section = ?");
    $subject_query->bind_param("ss", $year, $section);
    $subject_query->execute();
    $subjects_result = $subject_query->get_result();
} else {
    // If parent, fetch the LRN of the student
    $lrn = $user['lrn'];
    // Fetch subjects for the student associated with this parent
    $subject_query = $conn->prepare("SELECT s.subject_name, st.year, st.section FROM subjects_sections s 
                                      JOIN students st ON s.year_level = st.year AND s.section = st.section
                                      WHERE st.lrn = ?");
    $subject_query->bind_param("s", $lrn);
    $subject_query->execute();
    $subjects_result = $subject_query->get_result();

    // Fetch the year and section for displaying
    if ($subjects_result->num_rows > 0) {
        // Get the first student's year and section (assuming one student per parent)
        $student_data = $subjects_result->fetch_assoc();
        $year = $student_data['year'];
        $section = $student_data['section'];

        // Reset the result set to get all subjects
        $subjects_result->data_seek(0);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Subjects View</title>
</head>
<body>
    <h1><?php echo isset($_SESSION['student_logged_in']) ? "Your Subjects" : "Student's Subjects"; ?></h1>

    <?php if ($subjects_result->num_rows > 0): ?>
        <h2>Year <?php echo htmlspecialchars($year); ?> - Section <?php echo htmlspecialchars($section); ?></h2>
        <ul>
            <?php while ($subject = $subjects_result->fetch_assoc()): ?>
                <li>
                    <a href="student_subject_details.php?subject=<?php echo urlencode($subject['subject_name']); ?>">
                        <?php echo htmlspecialchars($subject['subject_name']); ?> - View
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No subjects available for this year and section.</p>
    <?php endif; ?>

    <h3>Actions</h3>
    <ul>
        <?php if (isset($_SESSION['student_logged_in'])): ?>
            <li><a href="student_dashboard.php">Back to Your Dashboard</a></li>
        <?php elseif (isset($_SESSION['parent_logged_in'])): ?>
            <li><a href="parent_dashboard.php">Back to Your Dashboard</a></li>
        <?php endif; ?>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</body>
</html>
