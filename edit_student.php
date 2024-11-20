<?php
session_start();
include('db.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Fetch student details
if (isset($_GET['lrn'])) {
    $lrn = $_GET['lrn'];

    // Validate and sanitize the input
    $lrn = htmlspecialchars($lrn);

    // Prepare statement to fetch student details
    $stmt = $conn->prepare("SELECT * FROM students WHERE lrn = ?");
    $stmt->bind_param("s", $lrn);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if student exists
    if ($result->num_rows === 0) {
        echo "Student not found.";
        exit();
    }

    $student = $result->fetch_assoc();
} else {
    echo "Invalid request.";
    exit();
}

// Update student details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $middle_initial = htmlspecialchars($_POST['middle_initial']);
    $email = htmlspecialchars($_POST['email']);
    $year = htmlspecialchars($_POST['year']);
    $section = htmlspecialchars($_POST['section']);

    // Prepare statement to update student details
    $update_stmt = $conn->prepare("UPDATE students SET first_name = ?, last_name = ?, middle_initial = ?, email = ?, year = ?, section = ? WHERE lrn = ?");
    $update_stmt->bind_param("ssssssi", $first_name, $last_name, $middle_initial, $email, $year, $section, $lrn);
    if ($update_stmt->execute()) {
        header("Location: users_students.php"); // Redirect to students list
        exit();
    } else {
        echo "Error updating student details.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Edit Student</title>
</head>
<body>
    <h1>Edit Student</h1>
    <form method="POST">
        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" value="<?php echo htmlspecialchars($student['first_name']); ?>" required>

        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" value="<?php echo htmlspecialchars($student['last_name']); ?>" required>

        <label for="middle_initial">Middle Initial:</label>
        <input type="text" name="middle_initial" value="<?php echo htmlspecialchars($student['middle_initial']); ?>" maxlength="1">

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>

        <label for="year">Year:</label>
        <input type="text" name="year" value="<?php echo htmlspecialchars($student['year']); ?>" required>

        <label for="section">Section:</label>
        <input type="text" name="section" value="<?php echo htmlspecialchars($student['section']); ?>" required>

        <button type="submit">Update Student</button>
    </form>
    <p><a href="users_students.php">Cancel</a></p>
</body>
</html>

