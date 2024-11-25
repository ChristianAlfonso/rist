<?php
include 'db.php';
$qry = "SELECT g.*, s.last_name,s.first_name, s.lrn 
FROM grades g 
JOIN students s ON g.student_lrn = s.lrn 
WHERE year = '7'";
$qry = mysqli_query($conn, $qry);
while($row = mysqli_fetch_array($qry)){
    echo $row['last_name'] . $row['first_name'] . " - " . $row['scores'] . "<br>";
}
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $year = $_POST['year'];
    $qry = "SELECT g.*, s.last_name,s.first_name, s.lrn 
    FROM grades g 
    JOIN students s ON g.student_lrn = s.lrn 
    WHERE year = '$year' ";
    $qry = mysqli_query($conn, $qry);
    while($row = mysqli_fetch_array($qry)){
        echo $row['last_name'] . $row['first_name'] . " - " . $row['scores'] . "<br>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking</title>
</head>
<body>
 <form action="ranking.php" method = "POST">
     <select name="year" id="year">
        <option value="7">7</option>
        <option value="8">8</option>
        <option value="9">9</option>
        <option value="10">10</option>
    </select>
    <select name="quarter" id="quarter">
        <option value="1">1st</option>
        <option value="2">2nd</option>
        <option value="3">3rd</option>
        <option value="4">4th</option>
    </select>
    <button type="submit">Submit</button>
 </form>
</body>
</html>