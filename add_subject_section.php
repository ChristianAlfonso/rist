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
$_SESSION['teacher_id'] = $teacher_id; // Save the teacher's ID in the session

// add 
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_name = $_POST['subject_name'];
    $year_level = $_POST['year_level'];
    $section = $_POST['section'];
    $school_year = $_POST['school_year']; 
// end

//  add school year and error message
    $check_query = $conn->prepare("SELECT * FROM subjects_sections WHERE subject_name = ? AND year_level = ? AND section = ? AND school_year = ?");
    $check_query->bind_param("ssss", $subject_name, $year_level, $section, $school_year);
    $check_query->execute();
    $existing = $check_query->get_result();

    if ($existing->num_rows > 0) {
        $error_message = "The combination of Subject, Year Level, Section, and School Year already exists!";
    } else {
   //     
        // add If no duplicate exists, proceed with the insertion
        $insert_query = $conn->prepare("INSERT INTO subjects_sections (subject_name, year_level, section, school_year, teacher_id) VALUES (?, ?, ?, ?, ?)");
        $insert_query->bind_param("sssss", $subject_name, $year_level, $section, $school_year, $teacher_id);
        $insert_query->execute();
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Subject/Section added successfully!',
                    });
                });
              </script>";
    }
}
// end


// Handle deletion of a subject/section
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_query = $conn->prepare("DELETE FROM subjects_sections WHERE id = ? AND teacher_id = ?");
    $delete_query->bind_param("ss", $delete_id, $teacher_id);
    $delete_query->execute();
}

//  add school year
$subjects_query = $conn->prepare("SELECT id, subject_name, year_level, section, school_year FROM subjects_sections WHERE teacher_id = ?");
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="add-subject-section vh-100 d-flex justify-content-center align-items-center">
    <div class="container mt-5 p-5 shadow">
        <h1>Add Subject/Section</h1>
          <!-- add error mess -->
    <?php if (!empty($error_message)): ?>
        <div style="color: red;"><?php echo $error_message; ?></div>
    <?php endif; ?>

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

                        <!--  add School Year Field -->
                <div class="form-group">
                    <label for="school_year">School Year:</label>
                    <input class="form-control" type="text" id="school_year" name="school_year" required>
                </div>
        
                <div class="form-group d-flex mt-3" style="gap: 5px; flex-wrap: wrap;">
                    <a class="btn btn-dark "href="teacher_dashboard.php">Back to Dashboard</a>
                    <button class="btn btn-danger" type="submit">Add Subject/Section</button>
                </div>

            </form>

   

    </div>
</div>
 <!--  add School Year -->
<h2>Your Added Subjects and Sections</h2>
    <?php if ($subjects_result->num_rows > 0): ?>
        <ul>
            <?php while ($subject = $subjects_result->fetch_assoc()): ?>
                <li>
                     <!--  add School Year -->
                    <?php echo htmlspecialchars($subject['year_level']) . " " . htmlspecialchars($subject['section']) . " (" . htmlspecialchars($subject['school_year']) . "): " . htmlspecialchars($subject['subject_name']); ?> 
                    
                    <a href="?delete_id=<?php echo urlencode($subject['id']); ?>" onclick="return confirm('Are you sure you want to delete this subject/section?');">Delete</a>

                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No subjects added yet.</p>
    <?php endif; ?>


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
