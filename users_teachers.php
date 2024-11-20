<?php
session_start();
include('db.php');

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Fetch teachers from the database
$teachers_query = "SELECT * FROM teachers";
$teachers_result = mysqli_query($conn, $teachers_query);

// Handle teacher deletion
if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);

    // First, delete any sections associated with the teacher
    $delete_sections_query = "DELETE FROM sections WHERE teacher_id = '$delete_id'";
    mysqli_query($conn, $delete_sections_query); // Ignore errors for sections

    // Then, delete the teacher
    $delete_query = "DELETE FROM teachers WHERE id = '$delete_id'";
    if (mysqli_query($conn, $delete_query)) {
        echo "<script>alert('Teacher and their sections deleted successfully!'); window.location='users_teachers.php';</script>";
    } else {
        echo "<script>alert('Error deleting teacher');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teachers</title>
</head>
<body>
    <h1>Registered Teachers</h1>
    <table border="1">
        <tr>
            <th>Teacher ID</th>
            <th>Subject</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($teachers_result)) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['id']); ?></td>
            <td><?php echo htmlspecialchars($row['subject']); ?></td>
            <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td>
                <a href="edit_teacher.php?id=<?php echo urlencode($row['id']); ?>">Edit</a> |
                <a href="users_teachers.php?delete_id=<?php echo urlencode($row['id']); ?>" onclick="return confirm('Are you sure you want to delete this teacher and their associated sections?');">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>
    
    <a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>
