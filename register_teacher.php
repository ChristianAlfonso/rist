<?php
include('db.php'); // Include your database connection

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id'],$_POST['subject'], $_POST['last_name'], $_POST['first_name'], $_POST['middle_initial'], $_POST['email'], $_POST['username'], $_POST['password'])) {

        // Sanitize and hash input values
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $subject = mysqli_real_escape_string($conn, $_POST['subject']);
        $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
        $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
        $middle_initial = mysqli_real_escape_string($conn, $_POST['middle_initial']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = password_hash(mysqli_real_escape_string($conn, $_POST['password']), PASSWORD_DEFAULT);

        // Insert teacher data
        $query = "INSERT INTO teachers (id, subject, last_name, first_name, middle_initial, email, username, password) 
                  VALUES ('$id', '$subject', '$last_name', '$first_name', '$middle_initial', '$email', '$username', '$password')";
        
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Teacher registered successfully!'); window.location='admin_dashboard.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "<script>alert('Error: Please fill out all required fields.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    
    <title>Register Teacher</title>
    <script>
        function togglePasswordVisibility() {
            const passwordField = document.getElementById('password');
            const toggleButton = document.getElementById('togglePassword');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleButton.innerText = 'Hide Password';
            } else {
                passwordField.type = 'password';
                toggleButton.innerText = 'Show Password';
            }
        }
    </script>
</head>
<body>
    <h2>Register Teacher</h2>
    <form method="POST" action="register_teacher.php">
        <label for="id">Teacher ID:</label><br>
        <input type="text" name="id" required><br>

        <label for="subject">Subject:</label><br>
        <input type="text" name="subject" required><br>
        
        <label for="last_name">Last Name:</label><br>
        <input type="text" name="last_name" required><br>

        <label for="first_name">First Name:</label><br>
        <input type="text" name="first_name" required><br>

        <label for="middle_initial">Middle Initial:</label><br>
        <input type="text" name="middle_initial" required><br>

        <label for="email">Email:</label><br>
        <input type="email" name="email" required><br>

        <label for="username">Username:</label><br>
        <input type="text" name="username" required><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br>
        <button type="button" id="togglePassword" onclick="togglePasswordVisibility()">Show Password</button><br><br>

        <button type="submit">Register</button>
    </form>
</body>
</html>
