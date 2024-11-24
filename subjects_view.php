<?php
session_start();
include('db.php');

// Check if the user is logged in as a student or parent
if (!isset($_SESSION['student_logged_in']) && !isset($_SESSION['parent_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Initialize year and section variables
$year = null;
$section = null;

// Fetch the student's or parent's LRN
if (isset($_SESSION['student_logged_in'])) {
    $username = $_SESSION['student_username'];
    $query = $conn->prepare("SELECT year, section FROM students WHERE username = ?");
} elseif (isset($_SESSION['parent_logged_in'])) {
    $username = $_SESSION['parent_username'];
    $query = $conn->prepare("SELECT lrn_students AS lrn FROM parents WHERE username = ?");
}

$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

// For students, get year and section
if (isset($_SESSION['student_logged_in'])) {
    $year = $user['year'];
    $section = $user['section'];

    // Fetch subjects for this student's year and section
    $subject_query = $conn->prepare("SELECT subject_name FROM subjects_sections WHERE year_level = ? AND section = ?");
    $subject_query->bind_param("ss", $year, $section);
    $subject_query->execute();
    $subjects_result = $subject_query->get_result();
} else {
    // If parent, fetch the LRN of the student
    $lrn = $user['lrn'];
    // Fetch subjects for the student associated with this parent
    $subject_query = $conn->prepare("SELECT s.subject_name, st.year, st.section FROM subjects_sections s 
                                      JOIN students st ON s.year_level = st.year AND s.section = st.section
                                      WHERE st.lrn = ?");
    $subject_query->bind_param("s", $lrn);
    $subject_query->execute();
    $subjects_result = $subject_query->get_result();

    // Fetch the year and section for displaying
    if ($subjects_result->num_rows > 0) {
        // Get the first student's year and section (assuming one student per parent)
        $student_data = $subjects_result->fetch_assoc();
        $year = $student_data['year'];
        $section = $student_data['section'];

        // Reset the result set to get all subjects
        $subjects_result->data_seek(0);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">  
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.bootstrap5.css">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    
    <title>Subjects View</title>
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
                <h1><?php echo isset($_SESSION['student_logged_in']) ? "Your Subjects" : "Student's Subjects"; ?></h1>

                <?php if ($subjects_result->num_rows > 0): ?>
                    <h2>Year <?php echo htmlspecialchars($year); ?> - Section <?php echo htmlspecialchars($section); ?></h2>
                    <table class="table table-bordered table-striped" id="example">
                        <thead>
                            <tr>
                                <th scope="col">Subject Name</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($subject = $subjects_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                    <td>
                                        <a href="student_subject_details.php?subject=<?php echo urlencode($subject['subject_name']); ?>" class="btn btn-danger ">
                                            View
                                        </a>

                                        
                                           
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No subjects available for this year and section.</p>
                <?php endif; ?>

                <div class="container-fluid mt-3 d-flex justify-content-end">
                     <a class="btn btn-dark"href="parent_dashboard.php">Back to dashboard</a>

                </div>

                  
            </div>
        </div>
                                
  

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.3/js/responsive.bootstrap5.js"></script>

  

    <script>
        new DataTable('#example', {
                responsive: true
            });

        // Auto close offcanvas when screen size changes
        window.addEventListener('resize', function() {
            if (window.innerWidth > 700) {
                var offcanvasElement = document.getElementById('demo');
                var offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasElement);
                if (offcanvasInstance) {
                    offcanvasInstance.hide();
                }
            }
        });
    </script>
</body>
</html>
