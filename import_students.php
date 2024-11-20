<?php
session_start();
include('db.php'); // Include your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['student_file'])) {
    $file = $_FILES['student_file']['tmp_name'];

    // Open and read the CSV file
    if (($handle = fopen($file, "r")) !== FALSE) {
        // Skip the first row (headers)
        $header = fgetcsv($handle, 1000, ",");

        // Process each row
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Extract student details from CSV
            $lrn = mysqli_real_escape_string($conn, $data[0]);
            $last_name = mysqli_real_escape_string($conn, $data[1]);
            $first_name = mysqli_real_escape_string($conn, $data[2]);
            $middle_initial = mysqli_real_escape_string($conn, $data[3]);
            $email = mysqli_real_escape_string($conn, $data[4]);
            $year = mysqli_real_escape_string($conn, $data[5]);
            $section = mysqli_real_escape_string($conn, $data[6]);
            $username = mysqli_real_escape_string($conn, $data[7]);
            $password = password_hash(mysqli_real_escape_string($conn, $data[8]), PASSWORD_DEFAULT);

            // Insert into the database
            $query = "INSERT INTO students (lrn, last_name, first_name, middle_initial, email, year, section, username, password) 
                      VALUES ('$lrn', '$last_name', '$first_name', '$middle_initial', '$email', '$year', '$section', '$username', '$password')";

            mysqli_query($conn, $query);
        }
        fclose($handle);
        echo "<script>alert('Students imported successfully!'); window.location='admin_dashboard.php';</script>";
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
    <title>Import Students (CSV)</title>
</head>
<body>
    <h2>Import Students (CSV)</h2>
    <form method="POST" enctype="multipart/form-data" action="import_students.php">
        <label for="student_file">Upload CSV File:</label><br>
        <input type="file" name="student_file" accept=".csv" required><br><br>
        <button type="submit">Import</button>
    </form>
</body>
</html>
