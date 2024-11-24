<?php
session_start();
include('db.php');

// Check if the student or parent is logged in
if (!isset($_SESSION['student_logged_in']) && !isset($_SESSION['parent_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Determine if the logged-in user is a student or a parent
$user_id = isset($_SESSION['student_logged_in']) ? $_SESSION['student_username'] : $_SESSION['parent_username'];
$role = isset($_SESSION['student_logged_in']) ? 'student' : 'parent';

// Fetch the LRN for students or their child's LRN for parents
if ($role === 'student') {
    $lrn = $user_id; // Student is logged in, use their LRN
} else {
    // Parent is logged in, fetch the child's LRN from the parents table
    $parent_query = "SELECT lrn_students FROM parents WHERE username='$user_id'";
    $parent_result = mysqli_query($conn, $parent_query);
    $parent_row = mysqli_fetch_assoc($parent_result);
    $lrn = $parent_row['lrn_students'];
}

// Get the selected quarter from the form (default to '1st' if not set)
$quarter = mysqli_real_escape_string($conn, $_GET['quarter'] ?? '1st');

// Fetch the subject from the URL or set it to an empty value
$subject_name = mysqli_real_escape_string($conn, $_GET['subject'] ?? '');

// Query to get the grades for the logged-in student or their child, filtered by quarter and subject
$grades_query = "SELECT g.*, ss.subject_name
                 FROM grades g
                 JOIN subjects_sections ss ON g.subject_id = ss.id
                 WHERE g.student_lrn='$lrn' AND g.quarter='$quarter' AND ss.subject_name='$subject_name'";

$grades_result = mysqli_query($conn, $grades_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grades for Quarter: <?php echo htmlspecialchars($quarter); ?> - <?php echo htmlspecialchars($subject_name); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">  
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.bootstrap5.css">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<style>
     /*
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
            */
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
               


                  <h1>Grades for Quarter: <?php echo htmlspecialchars($quarter); ?> - <?php echo htmlspecialchars($subject_name); ?></h1>

                    <!-- Quarter selection form -->
                    <form action="grade_view.php" method="get">
                        <label for="quarter">Select Quarter:</label>
                        <select class="form-control" name="quarter" id="quarter" onchange="this.form.submit()">
                            <option value="1st" <?php echo $quarter === '1st' ? 'selected' : ''; ?>>1st Quarter</option>
                            <option value="2nd" <?php echo $quarter === '2nd' ? 'selected' : ''; ?>>2nd Quarter</option>
                            <option value="3rd" <?php echo $quarter === '3rd' ? 'selected' : ''; ?>>3rd Quarter</option>
                            <option value="4th" <?php echo $quarter === '4th' ? 'selected' : ''; ?>>4th Quarter</option>
                        </select>
                        <!-- Hidden field to keep the subject in the URL -->
                        <input type="hidden" name="subject" value="<?php echo htmlspecialchars($subject_name); ?>">
                    </form>

                    <h2 class="mt-3">Your Grades</h2>
                    <?php if (mysqli_num_rows($grades_result) > 0): ?>
                        <table class="table table-bordered table-responsive" id="example">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Activity/Task</th>
                                    <th>Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($row = mysqli_fetch_assoc($grades_result)) {
                                    // Decode the scores from JSON format
                                    $scores = json_decode($row['scores'], true);
                                    echo "<tr>";
                                    echo "<td rowspan='" . (count($scores) + 1) . "'>" . htmlspecialchars($row['subject_name']) . "</td>";

                                    if (is_array($scores)) {
                                        foreach ($scores as $key => $value) {
                                            echo "<tr><td>" . htmlspecialchars($key) . "</td><td>" . htmlspecialchars($value) . "</td></tr>";
                                        }
                                    } else {
                                        echo "<td>No scores available</td><td></td>";
                                    }
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No grades found for the selected quarter and subject.</p>
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
         /*
        function printReport() {
            window.print();
        }
            */
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
