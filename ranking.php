<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $year = $_POST['year'];
    $qry = "SELECT g.*, s.last_name, s.first_name, s.lrn 
            FROM grades g 
            JOIN students s ON g.student_lrn = s.lrn 
            WHERE year = '$year'";
    $qry = mysqli_query($conn, $qry);
    $results = [];
    while ($row = mysqli_fetch_array($qry)) {
        $results[] = $row;
    }
    $_SESSION['ranking_results'] = $results;
    $_SESSION['ranking_submitted'] = true;
    header("Location: teacher_dashboard.php#ranking");
    exit();
} else {
    $qry = "SELECT g.*, s.last_name, s.first_name, s.lrn 
            FROM grades g 
            JOIN students s ON g.student_lrn = s.lrn 
            WHERE year = '7'";
    $qry = mysqli_query($conn, $qry);
    while ($row = mysqli_fetch_array($qry)) {
        echo $row['last_name'] . " " . $row['first_name'] . " - " . $row['scores'] . "<br>";
    }
}
?>
