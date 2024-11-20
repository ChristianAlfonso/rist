<?php
include('db.php'); // Include your database connection

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['lrn_students'], $_POST['last_name'], $_POST['first_name'], $_POST['middle_initial'], $_POST['email'], $_POST['username'], $_POST['password'])) {

        // Sanitize and hash input values
        $lrn_students = mysqli_real_escape_string($conn, $_POST['lrn_students']);
        $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
        $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
        $middle_initial = mysqli_real_escape_string($conn, $_POST['middle_initial']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = password_hash(mysqli_real_escape_string($conn, $_POST['password']), PASSWORD_DEFAULT);

        // Check if student exists
        $lrn_check_query = "SELECT * FROM students WHERE lrn = '$lrn_students'";
        $lrn_result = mysqli_query($conn, $lrn_check_query);

        if (mysqli_num_rows($lrn_result) > 0) {
            // Check if the parent is already registered for the same student's LRN
            $parent_check_query = "SELECT * FROM parents WHERE lrn_students = '$lrn_students'";
            $parent_result = mysqli_query($conn, $parent_check_query);

            if (mysqli_num_rows($parent_result) == 0) { // No duplicate parent for the same student
                // Insert parent data
                $query = "INSERT INTO parents (lrn_students, last_name, first_name, middle_initial, email, username, password) 
                          VALUES ('$lrn_students', '$last_name', '$first_name', '$middle_initial', '$email', '$username', '$password')";
                
                if (mysqli_query($conn, $query)) {
                    echo "<script>alert('Parent registered successfully!'); window.location='admin_dashboard.php';</script>";
                } else {
                    echo "Error: " . mysqli_error($conn);
                }
            } else {
                echo "<script>alert('Error: A parent for this student LRN already exists.');</script>";
            }
        } else {
            echo "<script>alert('Error: The student with LRN \"$lrn_students\" does not exist.');</script>";
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
    <title>Register Parent</title>
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
    <h2>Register Parent</h2>
    <form method="POST" action="register_parent.php">
        <label for="lrn_students">Student LRN:</label><br>
        <input type="text" name="lrn_students" required><br>
        
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
