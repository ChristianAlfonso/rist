<?php
session_start();
include('db.php'); // Include your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['parent_file'])) {
    $file = $_FILES['parent_file']['tmp_name'];

    // Open and read the CSV file
    if (($handle = fopen($file, "r")) !== FALSE) {
        // Skip the first row (headers)
        $header = fgetcsv($handle, 1000, ",");

        // Process each row
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Extract parent details from CSV
            $lrn_students = mysqli_real_escape_string($conn, $data[0]);
            $last_name = mysqli_real_escape_string($conn, $data[1]);
            $first_name = mysqli_real_escape_string($conn, $data[2]);
            $middle_initial = mysqli_real_escape_string($conn, $data[3]);
            $email = mysqli_real_escape_string($conn, $data[4]);
            $username = mysqli_real_escape_string($conn, $data[5]);
            $password = password_hash(mysqli_real_escape_string($conn, $data[6]), PASSWORD_DEFAULT);

            // Check if LRN (student) and parent username already exist
            $check_query = "SELECT * FROM parents WHERE lrn_students = '$lrn_students' OR username = '$username'";
            $check_result = mysqli_query($conn, $check_query);

            if (mysqli_num_rows($check_result) == 0) {
                // If no duplicate, insert the new record
                $query = "INSERT INTO parents (lrn_students, last_name, first_name, middle_initial, email, username, password) 
                          VALUES ('$lrn_students', '$last_name', '$first_name', '$middle_initial', '$email', '$username', '$password')";
                mysqli_query($conn, $query);
            } else {
                echo "<script>console.log('Parent with LRN $lrn_students or username $username already exists. Skipping.');</script>";
            }
        }
        fclose($handle);
        echo "<script>alert('Parents imported successfully!'); window.location='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error: Unable to open file.');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">:
    <title>Import Parents (CSV)</title>
</head>
<body>
    <h2>Import Parents (CSV)</h2>
    <form method="POST" enctype="multipart/form-data" action="import_parents.php">
        <label for="parent_file">Upload CSV File:</label><br>
        <input type="file" name="parent_file" accept=".csv" required><br><br>
        <button type="submit">Import</button>
    </form>
</body>
</html>
