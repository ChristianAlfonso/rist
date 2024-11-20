<?php
include('db.php'); // Include your database connection

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['lrn'], $_POST['last_name'], $_POST['first_name'], $_POST['middle_initial'], $_POST['email'], $_POST['year'], $_POST['section'], $_POST['username'], $_POST['password'])) {

        // Sanitize and hash input values
        $lrn = mysqli_real_escape_string($conn, $_POST['lrn']);
        $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
        $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
        $middle_initial = mysqli_real_escape_string($conn, $_POST['middle_initial']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $year = mysqli_real_escape_string($conn, $_POST['year']);
        $section = mysqli_real_escape_string($conn, $_POST['section']);
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = password_hash(mysqli_real_escape_string($conn, $_POST['password']), PASSWORD_DEFAULT);

        // Insert student data
        $query = "INSERT INTO students (lrn, last_name, first_name, middle_initial, email, year, section, username, password) 
                  VALUES ('$lrn', '$last_name', '$first_name', '$middle_initial', '$email', '$year', '$section', '$username', '$password')";
        
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Student registered successfully!'); window.location='admin_dashboard.php';</script>";
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
    
    <title>Register Student</title>
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
    <h2>Register Student</h2>
    <form method="POST" action="register_student.php">
        <label for="lrn">LRN:</label><br>
        <input type="text" name="lrn" required><br>
        
        <label for="last_name">Last Name:</label><br>
        <input type="text" name="last_name" required><br>

        <label for="first_name">First Name:</label><br>
        <input type="text" name="first_name" required><br>

        <label for="middle_initial">Middle Initial:</label><br>
        <input type="text" name="middle_initial" required><br>

        <label for="email">Email:</label><br>
        <input type="email" name="email" required><br>

        <label for="year">Year:</label><br>
        <input type="text" name="year" required><br>

        <label for="section">Section:</label><br>
        <input type="text" name="section" required><br>

        <label for="username">Username:</label><br>
        <input type="text" name="username" required><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br>
        <button type="button" id="togglePassword" onclick="togglePasswordVisibility()">Show Password</button><br><br>

        <button type="submit">Register</button>
    </form>
</body>
</html>
