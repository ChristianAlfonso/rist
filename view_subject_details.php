<?php
session_start();
include('db.php');

// Check if the teacher is logged in
if (!isset($_SESSION['teacher_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Get the year, section, and subject from the URL parameters
$year = $_GET['year'] ?? '';
$section = $_GET['section'] ?? '';
$subject = $_GET['subject'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <title>View Actions for <?php echo htmlspecialchars($subject); ?></title>
    <style>
        .blurred-background {
            background: url('images/bg.jpg') no-repeat center center;
            background-size: cover;
            filter: blur(8px);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .container {
            background-color: white;
            position: relative;
            z-index: 1;
        }

        .btn-equal {
            width: 180px;
        }
    </style>
</head>
<body>
    <div class="vh-100 d-flex justify-content-center align-items-center position-relative p-5">
        <div class="blurred-background"></div>
        <div class="container card p-5 shadow">
        <div class="display-3 text-center"> <?php echo htmlspecialchars($year . 'YR ' . $section . ': ' . $subject); ?></div>
    
 

            <div class="form-group d-flex justify-content-center " style="gap: 1rem; flex-wrap: wrap">
                <a class="btn btn-success btn-equal" href="attendance.php?year=<?php echo urlencode($year); ?>&section=<?php echo urlencode($section); ?>&subject=<?php echo urlencode($subject); ?>">Attendance</a>

                <a class="btn btn-danger btn-equal" href="grade.php?year=<?php echo urlencode($year); ?>&section=<?php echo urlencode($section); ?>&subject=<?php echo urlencode($subject); ?>">Grade</a>

                <a class="btn btn-dark btn-equal" href="teacher_dashboard.php">Back to Dashboard</a>
            </div>
        </div>
    </div>


  

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>