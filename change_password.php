<?php
session_start();
include('db.php');

if (!isset($_SESSION['student_logged_in']) && !isset($_SESSION['parent_logged_in']) && !isset($_SESSION['teacher_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Handle password change
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password) {
        // Fetch user info based on role
        if (isset($_SESSION['student_logged_in'])) {
            $username = $_SESSION['student_username'];
            $query = "SELECT * FROM students WHERE username = '$username'";
        } elseif (isset($_SESSION['parent_logged_in'])) {
            $username = $_SESSION['parent_username'];
            $query = "SELECT * FROM parents WHERE username = '$username'";
        } elseif (isset($_SESSION['teacher_logged_in'])) {
            $username = $_SESSION['teacher_username'];
            $query = "SELECT * FROM teachers WHERE username = '$username'";
        }
        
        $result = mysqli_query($conn, $query);
        $user = mysqli_fetch_assoc($result);

        // Verify current password
        if (password_verify($current_password, $user['password'])) {
            // Hash new password
            $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);
            // Update password in database
            $update_query = "UPDATE " . (isset($_SESSION['student_logged_in']) ? "students" : (isset($_SESSION['parent_logged_in']) ? "parents" : "teachers")) . " SET password='$new_password_hashed' WHERE username='$username'";
            
            if (mysqli_query($conn, $update_query)) {
                echo "<script>alert('Password changed successfully!'); window.location='" . (isset($_SESSION['student_logged_in']) ? "student_dashboard.php" : (isset($_SESSION['parent_logged_in']) ? "parent_dashboard.php" : "teacher_dashboard.php")) . "';</script>";
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        } else {
            echo "<script>alert('Current password is incorrect.');</script>";
        }
    } else {
        echo "<script>alert('New passwords do not match.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
</head>
<body>
    <h2>Change Password</h2>
    <form method="POST" action="change_password.php">
        <label for="current_password">Current Password:</label><br>
        <input type="password" name="current_password" required><br>

        <label for="new_password">New Password:</label><br>
        <input type="password" name="new_password" required><br>

        <label for="confirm_password">Confirm New Password:</label><br>
        <input type="password" name="confirm_password" required><br><br>

        <button type="submit">Change Password</button>
    </form>
    <a href="<?php echo (isset($_SESSION['student_logged_in']) ? "student_dashboard.php" : (isset($_SESSION['parent_logged_in']) ? "parent_dashboard.php" : "teacher_dashboard.php")); ?>">Cancel</a>
</body>
</html>