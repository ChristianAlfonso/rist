<?php
session_start();
include('db.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Fetch parent details
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Validate and sanitize the input
    $id = htmlspecialchars($id);

    // Prepare statement to fetch parent details
    $stmt = $conn->prepare("SELECT * FROM parents WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if parent exists
    if ($result->num_rows === 0) {
        echo "Parent not found.";
        exit();
    }

    $parent = $result->fetch_assoc();
} else {
    echo "Invalid request.";
    exit();
}

// Update parent details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $middle_initial = htmlspecialchars($_POST['middle_initial']);
    $email = htmlspecialchars($_POST['email']);
    $lrn_students = htmlspecialchars($_POST['lrn_students']); // Assuming you want to keep track of the student they are associated with

    // Prepare statement to update parent details
    $update_stmt = $conn->prepare("UPDATE parents SET first_name = ?, last_name = ?, middle_initial = ?, email = ?, lrn_students = ? WHERE id = ?");
    $update_stmt->bind_param("sssssi", $first_name, $last_name, $middle_initial, $email, $lrn_students, $id);
    if ($update_stmt->execute()) {
        header("Location: users_parents.php"); // Redirect to parents list
        exit();
    } else {
        echo "Error updating parent details.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Parent</title>
</head>
<body>
    <h1>Edit Parent</h1>
    <form method="POST">
        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" value="<?php echo htmlspecialchars($parent['first_name']); ?>" required>

        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" value="<?php echo htmlspecialchars($parent['last_name']); ?>" required>

        <label for="middle_initial">Middle Initial:</label>
        <input type="text" name="middle_initial" value="<?php echo htmlspecialchars($parent['middle_initial']); ?>" maxlength="1">

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($parent['email']); ?>" required>

        <label for="lrn_students">LRN of Student:</label>
        <input type="text" name="lrn_students" value="<?php echo htmlspecialchars($parent['lrn_students']); ?>" required>

        <button type="submit">Update Parent</button>
    </form>
    <p><a href="users_parents.php">Cancel</a></p>
</body>
</html>
