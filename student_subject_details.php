<?php
session_start();
include('db.php');

// Check if the user is logged in as a student or parent
if (!isset($_SESSION['student_logged_in']) && !isset($_SESSION['parent_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Get the subject from the URL
if (isset($_GET['subject'])) {
    $subject_name = $_GET['subject'];
} else {
    // Redirect if no subject is provided
    header("Location: subjects_view.php");
    exit();
}

// Initialize year and section variables
$year = null;
$section = null;

// Fetch the student's or parent's year and section
if (isset($_SESSION['student_logged_in'])) {
    $username = $_SESSION['student_username'];
    $query = $conn->prepare("SELECT year, section FROM students WHERE username = ?");
    $query->bind_param("s", $username);
    $query->execute();
    $result = $query->get_result();
    $user = $result->fetch_assoc();

    $year = $user['year'];
    $section = $user['section'];
} elseif (isset($_SESSION['parent_logged_in'])) {
    $username = $_SESSION['parent_username'];
    $query = $conn->prepare("SELECT lrn_students AS lrn FROM parents WHERE username = ?");
    $query->bind_param("s", $username);
    $query->execute();
    $result = $query->get_result();
    $user = $result->fetch_assoc();

    $lrn = $user['lrn'];
    // Fetch the year and section for the student associated with this parent
    $student_query = $conn->prepare("SELECT year, section FROM students WHERE lrn = ?");
    $student_query->bind_param("s", $lrn);
    $student_query->execute();
    $student_result = $student_query->get_result();
    
    if ($student_result->num_rows > 0) {
        $student_data = $student_result->fetch_assoc();
        $year = $student_data['year'];
        $section = $student_data['section'];
    } else {
        // Handle the case where no student is found
        echo "<p>No student found associated with this parent.</p>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <title><?php echo htmlspecialchars($subject_name); ?> - Details</title>
</head>
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
<body>

<div class="vh-100 d-flex justify-content-center align-items-center position-relative p-5">
    <div class="blurred-background"></div>
        <div class="container card p-5 shadow">
            <div class="display-3 text-center">  <h1><?php echo htmlspecialchars($subject_name); ?> - Options</h1>
            </div>
        
    

                <div class="form-group d-flex justify-content-center " style="gap: 1rem; flex-wrap: wrap">
                    <a class="btn btn-success" href="attendance_view.php?subject=<?php echo urlencode($subject_name); ?>">Attendance</a>
                    <a class="btn btn-danger" href="grade_view.php?subject=<?php echo urlencode($subject_name); ?>">Grade</a>
                    <a class="btn btn-primary" href="subjects_view.php">Back to Subjects</a>
                    <a class="btn btn-dark btn-equal" href="teacher_dashboard.php">Back to Dashboard</a>
                </div>
        </div>
</div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
