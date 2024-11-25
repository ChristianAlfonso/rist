<?php
session_start();
include 'db.php';
$year = $_GET['year'];
$section = $_GET['section'];    
$qry = "SELECT subject_name FROM subjects_sections WHERE year_level = '$year' AND section = '$section'";
$qry = mysqli_query($conn, $qry);
$subjects = [];


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Ranking</title>
</head>
<body>
    <?php
 while ($row = mysqli_fetch_array($qry)) {
    $subjects[] = $row['subject_name'];
    echo "<a href = 'ranking.php?year=$year&section=$section&subject={$row['subject_name']}'>" . $row['subject_name'] . "</a>";
}
    ?>
    
</body>
</html>