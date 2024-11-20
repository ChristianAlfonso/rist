<?php
session_start();
include('db.php');

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Fetch students from the database
$students_query = "SELECT * FROM students";
$students_result = mysqli_query($conn, $students_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
</head>
<body>
    <h1>Registered Students</h1>
    <table border="1">
        <tr>
            <th>LRN</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Year</th>
            <th>Section</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($students_result)) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['lrn']); ?></td>
            <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['year']); ?></td>
            <td><?php echo htmlspecialchars($row['section']); ?></td>
            <td>
                <a href="edit_student.php?lrn=<?php echo urlencode($row['lrn']); ?>">Edit</a> |
                <a href="users_students.php?delete_lrn=<?php echo urlencode($row['lrn']); ?>" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <?php
    // Handle student deletion
    if (isset($_GET['delete_lrn'])) {
        $delete_lrn = mysqli_real_escape_string($conn, $_GET['delete_lrn']);
        $delete_query = "DELETE FROM students WHERE lrn = '$delete_lrn'";
        if (mysqli_query($conn, $delete_query)) {
            echo "<script>alert('Student deleted successfully!'); window.location='users_students.php';</script>";
        } else {
            echo "<script>alert('Error deleting student');</script>";
        }
    }
    ?>
    
    <a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>
