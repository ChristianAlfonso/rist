<?php
// Database connection
$host = "localhost";
$username = "root";  // Change this if necessary
$password = "";  // Change this if necessary
$database = "cs";  // Change this to your database name

$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
