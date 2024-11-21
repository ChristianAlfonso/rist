<?php
session_start();
include('db.php'); // Include your database connection
$email = $_SESSION['email'];
$tables = ['admin', 'teachers', 'parents', 'students'];
for($i = 0; $i < count($tables); $i++) {
    $query = "SELECT * FROM $tables[$i] WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        echo "Changing password for {$user['username']}";
        break;
    }
    
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $hash_password = password_hash($password, PASSWORD_DEFAULT);
    if ($password === $confirm_password) {
        $query = "UPDATE $tables[$i] SET password='$hash_password' WHERE email='$email'";
        if (mysqli_query($conn, $query)) {
            echo "Password reset successfully.";
            session_destroy();
            header("Location: login.php");
        } else {
            echo "Failed to reset password.";
        }
    } else {
        echo "Passwords do not match.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset password</title>
</head>
<body>
    <h1>Change password</h1>
    <form action="reset_password.php" method="post">
        <label for="password">New password</label>
        <input type="password" name="password" id="password" required>
        <label for="confirm_password">Confirm password</label>
        <input type="password" name="confirm_password" id="confirm_password" required>
        <button type="submit">Submit</button>
    </form>
</body>

</html>