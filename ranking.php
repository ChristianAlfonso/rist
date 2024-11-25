<?php
session_start();
include 'db.php';
$year = $_GET['year'];
$section = $_GET['section'];
$subject = $_GET['subject'];
    $qry = "SELECT g.*, s.last_name, s.first_name, s.lrn 
            FROM grades g 
            JOIN students s ON g.student_lrn = s.lrn
            JOIN subjects_sections ss ON g.subject_id = ss.id
            WHERE s.year = '$year' AND ss.section = '$section' AND ss.subject_name = '$subject'
            ORDER BY g.scores DESC
            ";
    $qry = mysqli_query($conn, $qry);
    $results = [];

    

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking <?php echo $subject ?></title>
</head>
<body>
    <h1>Ranking in <?php echo $subject ?> </h1>
    <div class="container">
        <?php
 while ($row = mysqli_fetch_array($qry)) {
        $results[] = $row;

        echo $row['last_name'] . " " . $row['first_name'] . " - " . $row['scores'] . "<br>";
    }
        ?>
    </div>
</body>
</html>