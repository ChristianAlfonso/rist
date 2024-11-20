<?php
session_start();
include('db.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Fetch teacher details
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Validate and sanitize the input
    $id = htmlspecialchars($id);

    // Prepare statement to fetch teacher details
    $stmt = $conn->prepare("SELECT * FROM teachers WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if teacher exists
    if ($result->num_rows === 0) {
        echo "Teacher not found.";
        exit();
    }

    $teacher = $result->fetch_assoc();
} else {
    echo "Invalid request.";
    exit();
}

// Update teacher details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $middle_initial = htmlspecialchars($_POST['middle_initial']);
    $email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);

    // Prepare statement to update teacher details
    $update_stmt = $conn->prepare("UPDATE teachers SET first_name = ?, last_name = ?, middle_initial = ?, email = ?, subject = ? WHERE id = ?");
    $update_stmt->bind_param("ssssss", $first_name, $last_name, $middle_initial, $email, $subject, $id);
    if ($update_stmt->execute()) {
        header("Location: users_teachers.php"); // Redirect to teachers list
        exit();
    } else {
        echo "Error updating teacher details.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Edit Teacher</title>
</head>
<body>
    <h1>Edit Teacher</h1>
    <form method="POST">
        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" value="<?php echo htmlspecialchars($teacher['first_name']); ?>" required>

        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" value="<?php echo htmlspecialchars($teacher['last_name']); ?>" required>

        <label for="middle_initial">Middle Initial:</label>
        <input type="text" name="middle_initial" value="<?php echo htmlspecialchars($teacher['middle_initial']); ?>" maxlength="1">

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($teacher['email']); ?>" required>

        <label for="subject">Subject:</label>
        <input type="text" name="subject" value="<?php echo htmlspecialchars($teacher['subject']); ?>" required>

        <button type="submit">Update Teacher</button>
    </form>
    <p><a href="users_teachers.php">Cancel</a></p>
</body>
</html>
