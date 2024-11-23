<?php
session_start();
include('db.php');

// Check if the teacher is logged in
if (!isset($_SESSION['teacher_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Fetch the logged-in teacher's information
$username = $_SESSION['teacher_username'];
$query = $conn->prepare("SELECT id, subject, first_name, last_name, email FROM teachers WHERE username = ?");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();
$teacher = $result->fetch_assoc();
$teacher_id = $teacher['id']; // Get the teacher's ID

// Handle form submission for adding a subject/section
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_name = $_POST['subject_name'];
    $year_level = $_POST['year_level'];
    $section = $_POST['section'];

    $insert_query = $conn->prepare("INSERT INTO subjects_sections (subject_name, year_level, section, teacher_id) VALUES (?, ?, ?, ?)");
    $insert_query->bind_param("ssss", $subject_name, $year_level, $section, $teacher_id);
    $insert_query->execute();
}

// Handle deletion of a subject/section
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_query = $conn->prepare("DELETE FROM subjects_sections WHERE id = ? AND teacher_id = ?");
    $delete_query->bind_param("ss", $delete_id, $teacher_id);
    $delete_query->execute();
}

// Fetch subjects and sections added by this teacher
$subjects_query = $conn->prepare("SELECT id, subject_name, year_level, section FROM subjects_sections WHERE teacher_id = ?");
$subjects_query->bind_param("s", $teacher_id);
$subjects_query->execute();
$subjects_result = $subjects_query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">    
    <title>Add Subject/Section</title>
    <style>
        body {
            background: url('images/bg.jpg') no-repeat center center fixed;
            background-size: cover;
            backdrop-filter: blur(10px);
        }
        .container {
            background: white;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<div class="add-subject-section vh-100 d-flex justify-content-center align-items-center">
    <div class="container mt-5 p-5 shadow">
        <h1>Add Subject/Section</h1>
            <form method="POST" action="" class="p-3">

                <div class="form-group">
                    <label for="subject_name">Subject Name:</label>
                    <input class="form-control" type="text" id="subject_name" name="subject_name" required>
                </div> 
                
                <div class="form-group">
                    <label for="year_level">Year Level:</label>
                    <input class="form-control" type="text" id="year_level" name="year_level" required>
                </div>

                <div class="form-group">
                    <label for="section">Section:</label>
                    <input class="form-control" type="text" id="section" name="section" required>
                </div>

                <div class="form-group d-flex mt-3" style="gap: 5px; flex-wrap: wrap;">
                    <a class="btn btn-dark "href="teacher_dashboard.php">Back to Dashboard</a>
                    <button class="btn btn-danger" type="submit">Add Subject/Section</button>
                </div>

            </form>

   

    </div>
</div>

   


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script>
        // Auto close offcanvas when screen size changes
        window.addEventListener('resize', function() {
            if (window.innerWidth > 700) {
                var offcanvasElement = document.getElementById('offcanvasSidebar');
                var offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasElement);
                if (offcanvasInstance) {
                    offcanvasInstance.hide();
                }
            }
        });
    </script>
</body>
</html>
