<?php
session_start();
include('db.php');

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Fetch parents from the database
$parents_query = "SELECT * FROM parents";
$parents_result = mysqli_query($conn, $parents_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Parents</title>
</head>
<body>
    <h1>Registered Parents</h1>
    <table border="1">
        <tr>
            <th>Parent ID</th>
            <th>LRN Student</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($parents_result)) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['id']); ?></td>
            <td><?php echo htmlspecialchars($row['lrn_students']); ?></td>
            <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td>
                <a href="edit_parent.php?id=<?php echo urlencode($row['id']); ?>">Edit</a> |
                <a href="users_parents.php?delete_id=<?php echo urlencode($row['id']); ?>" onclick="return confirm('Are you sure you want to delete this parent?');">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <?php
    // Handle parent deletion
    if (isset($_GET['delete_id'])) {
        $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
        $delete_query = "DELETE FROM parents WHERE id = '$delete_id'";
        if (mysqli_query($conn, $delete_query)) {
            echo "<script>alert('Parent deleted successfully!'); window.location='users_parents.php';</script>";
        } else {
            echo "<script>alert('Error deleting parent');</script>";
        }
    }
    ?>
    
    <a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>
