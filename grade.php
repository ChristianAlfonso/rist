<?php
session_start();
include('db.php');

// Check if the teacher is logged in
if (!isset($_SESSION['teacher_logged_in'])) {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id']; 
$year = mysqli_real_escape_string($conn, $_GET['year']);
$section = mysqli_real_escape_string($conn, $_GET['section']);
$subject_name = mysqli_real_escape_string($conn, $_GET['subject']);
$quarter = mysqli_real_escape_string($conn, $_POST['quarter'] ?? '1st'); // Default to 1st quarter if not set

// Get the subject ID
$subject_query = "SELECT id FROM subjects_sections WHERE subject_name='$subject_name' AND year_level='$year' AND section='$section' AND teacher_id='$teacher_id'";
$subject_result = mysqli_query($conn, $subject_query);
$subject = mysqli_fetch_assoc($subject_result);
$subject_id = $subject['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['csv_file'])) {
    if ($_FILES['csv_file']['type'] === 'text/csv') {
        $file = fopen($_FILES['csv_file']['tmp_name'], 'r');
        $headers = fgetcsv($file); // Read the header row for dynamic columns

        while (($data = fgetcsv($file)) !== FALSE) {
            $student_lrn = mysqli_real_escape_string($conn, $data[0]);

            // Prepare a dynamic score array based on CSV headers
            $scores = [];
            for ($i = 1; $i < count($headers); $i++) {
                $score_key = $headers[$i];
                $score_value = mysqli_real_escape_string($conn, $data[$i]);
                $scores[$score_key] = $score_value;
            }
            $scores_json = json_encode($scores);

            // Check if student exists
            $check_student_query = "SELECT lrn FROM students WHERE lrn='$student_lrn'";
            $check_student_result = mysqli_query($conn, $check_student_query);

            if (mysqli_num_rows($check_student_result) > 0) {
                // Check if a grade entry already exists for this student, subject, and quarter
                $existing_grade_query = "SELECT * FROM grades WHERE student_lrn='$student_lrn' AND subject_id='$subject_id' AND quarter='$quarter'";
                $existing_grade_result = mysqli_query($conn, $existing_grade_query);

                if (mysqli_num_rows($existing_grade_result) > 0) {
                    // Update the existing record with new scores
                    $update_query = "UPDATE grades 
                                     SET scores='$scores_json' 
                                     WHERE student_lrn='$student_lrn' AND subject_id='$subject_id' AND quarter='$quarter'";
                    mysqli_query($conn, $update_query);
                } else {
                    // Insert new grade entry if no existing record found
                    $insert_query = "INSERT INTO grades (student_lrn, subject_id, quarter, scores, teacher_id) 
                                     VALUES ('$student_lrn', '$subject_id', '$quarter', '$scores_json', '$teacher_id')";
                    mysqli_query($conn, $insert_query);
                }
            } else {
                echo "<script>alert('Student LRN $student_lrn does not exist.');</script>";
            }
        }
        fclose($file);
        echo "<script>alert('Grade data processed successfully.');</script>";
    } else {
        echo "<script>alert('Please upload a valid CSV file.');</script>";
    }
}

// Query to fetch uploaded grades for the selected quarter
$grades_query = "SELECT g.*, s.last_name, s.first_name 
                 FROM grades g
                 JOIN students s ON g.student_lrn = s.lrn 
                 WHERE g.teacher_id='$teacher_id' 
                   AND g.subject_id='$subject_id' 
                   AND g.quarter='$quarter' 
                 ORDER BY s.last_name";
$grades_result = mysqli_query($conn, $grades_query);
$grades = mysqli_fetch_assoc($grades_result);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Grades for <?php echo htmlspecialchars($subject_name); ?> (Year: <?php echo $year; ?>, Section: <?php echo $section; ?>, Quarter: <?php echo $quarter; ?>)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">  
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.bootstrap5.css">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <style>

      
        @media print {
            body * {
                visibility: hidden;
            }
            #printable-area, #printable-area * {
                visibility: visible;
            }
            #printable-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
            
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
    <div class="blurred-background"></div>

    <div class="container-fluid p-5 d-flex justify-content-center align-items-center">
    <div class="container shadow mt-3 p-5">
        <h2>Upload or Update Grades for <?php echo htmlspecialchars($subject_name); ?> (Year: <?php echo $year; ?>, Section: <?php echo $section; ?>)</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="quarter">Select Quarter:</label>
                        <select class="form-control" name="quarter" id="quarter" required onchange="this.form.submit()">
                            <option value="1st" <?php echo $quarter === '1st' ? 'selected' : ''; ?>>1st Quarter</option>
                            <option value="2nd" <?php echo $quarter === '2nd' ? 'selected' : ''; ?>>2nd Quarter</option>
                            <option value="3rd" <?php echo $quarter === '3rd' ? 'selected' : ''; ?>>3rd Quarter</option>
                            <option value="4th" <?php echo $quarter === '4th' ? 'selected' : ''; ?>>4th Quarter</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="csv_file">Upload Grades (CSV):</label>
                        <input class="form-control" type="file" name="csv_file" id="csv_file" accept=".csv" required>
                    </div>
                </div>
            </div>
            <div class="form-group mt-3">
                <a class="btn btn-dark btn-equal" href="teacher_dashboard.php">Back to Dashboard</a>
                <input class="btn btn-danger btn-equal" type="submit" value="Upload Grades">
            </div>
        </form>

        <h2 class="mt-5">Uploaded Grades for <?php echo $quarter; ?> Quarter</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="example">
                <thead>
                    <tr>
                        <th>LRN</th>
                        <th>Name</th>
                        <th>Grades</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Reset pointer to the result set and display all grades
                    mysqli_data_seek($grades_result, 0);
                    $arr = [];
                    while ($row = mysqli_fetch_assoc($grades_result)) {
                        // Attempt to decode the scores safely
                      
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['student_lrn']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['scores']) . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
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
         
        function printReport() {
            window.print();
        }
            
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
