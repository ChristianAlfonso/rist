<?php
session_start();
include('db.php'); // Include your database connection

// Check if the user is logged in as a teacher
if (!isset($_SESSION['teacher_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Define the teacher_id from the session
$teacher_id = $_SESSION['teacher_username']; // Assuming this is how you store the teacher's ID

// Get the year, section, and subject from the URL
$year = mysqli_real_escape_string($conn, $_GET['year']);
$section = mysqli_real_escape_string($conn, $_GET['section']);
$subject_name = mysqli_real_escape_string($conn, $_GET['subject']);

// Fetch subject ID based on the subject name
$subject_query = "SELECT id FROM subjects_sections WHERE subject_name='$subject_name' AND year_level='$year' AND section='$section' AND teacher_id='$teacher_id'";
$subject_result = mysqli_query($conn, $subject_query);
$subject = mysqli_fetch_assoc($subject_result);
$subject_id = $subject['id'];

// Handle CSV upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['csv_file'])) {
    if ($_FILES['csv_file']['type'] === 'text/csv') {
        $file = fopen($_FILES['csv_file']['tmp_name'], 'r');
        $dates = fgetcsv($file);
        array_shift($dates); // Remove LRN column
        array_shift($dates); // Remove Name column

        while (($data = fgetcsv($file)) !== FALSE) {
            $student_lrn = mysqli_real_escape_string($conn, $data[0]);
            $name = mysqli_real_escape_string($conn, $data[1]);

            $check_student_query = "SELECT lrn FROM students WHERE lrn='$student_lrn'";
            $check_student_result = mysqli_query($conn, $check_student_query);

            if (mysqli_num_rows($check_student_result) > 0) {
                foreach ($dates as $index => $date_string) {
                    $date = DateTime::createFromFormat('m/d/Y', trim($date_string));
                    if ($date) {
                        $date_formatted = $date->format('Y-m-d');
                        $status = mysqli_real_escape_string($conn, $data[$index + 2]);

                        $query = "INSERT INTO attendance (student_lrn, subject_id, date, status, teacher_id) 
                                  VALUES ('$student_lrn', '$subject_id', '$date_formatted', '$status', '$teacher_id')
                                  ON DUPLICATE KEY UPDATE status='$status'";
                        mysqli_query($conn, $query);
                    }
                }
            } else {
                echo "<script>alert('Student LRN $student_lrn does not exist in the database.');</script>";
            }
        }
        fclose($file);
        echo "<script>alert('Attendance records uploaded successfully.');</script>";
    } else {
        echo "<script>alert('Please upload a valid CSV file.');</script>";
    }
}

// Handle date editing
if (isset($_GET['edit_date']) && isset($_GET['new_date'])) {
    $old_date = mysqli_real_escape_string($conn, $_GET['edit_date']);
    $new_date = mysqli_real_escape_string($conn, $_GET['new_date']);
    $update_query = "UPDATE attendance SET date='$new_date' WHERE date='$old_date' AND subject_id='$subject_id' AND teacher_id='$teacher_id'";
    mysqli_query($conn, $update_query);
    echo "<script>alert('Date updated successfully.'); window.location.href = 'attendance.php?year=$year&section=$section&subject=$subject_name';</script>";
}

// Handle date deletion
if (isset($_GET['delete_date'])) {
    $date_to_delete = mysqli_real_escape_string($conn, $_GET['delete_date']);
    $delete_query = "DELETE FROM attendance WHERE date='$date_to_delete' AND subject_id='$subject_id' AND teacher_id='$teacher_id'";
    mysqli_query($conn, $delete_query);
    echo "<script>alert('Date deleted successfully.'); window.location.href = 'attendance.php?year=$year&section=$section&subject=$subject_name';</script>";
}

// Group dates by month and year
$month_query = "SELECT DISTINCT DATE_FORMAT(date, '%Y-%m') AS month FROM attendance WHERE subject_id='$subject_id' AND teacher_id='$teacher_id' ORDER BY month DESC";
$month_result = mysqli_query($conn, $month_query);
$months = [];
while ($month_row = mysqli_fetch_assoc($month_result)) {
    $months[] = $month_row['month'];
}

// If a month is selected, display attendance for that month
$selected_month = isset($_GET['month']) ? mysqli_real_escape_string($conn, $_GET['month']) : '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Upload</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">  
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.bootstrap5.css">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">    
    <style>
        @media print {
            body * { visibility: hidden; }
            #printableTable, #printableTable * { visibility: visible; }
            #printableTable { position: absolute; top: 0; left: 0; width: 100%; }
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
    <script>
        function printReport() {
            window.print();
        }

        function editDate(date) {
            let newDate = prompt("Enter new date (YYYY-MM-DD):", date);
            if (newDate) {
                window.location.href = `attendance.php?year=<?php echo $year; ?>&section=<?php echo $section; ?>&subject=<?php echo $subject_name; ?>&edit_date=${date}&new_date=${newDate}`;
            }
        }

        function deleteDate(date) {
            if (confirm("Are you sure you want to delete all attendance records for this date?")) {
                window.location.href = `attendance.php?year=<?php echo $year; ?>&section=<?php echo $section; ?>&subject=<?php echo $subject_name; ?>&delete_date=${date}`;
            }
        }
    </script>
</head>
<body>
    <div class="blurred-background"></div>
    <div class="container-fluid p-5 d-flex justify-content-center align-items-center">
        <div class="container shadow mt-5 p-5">
            <h2>Upload Attendance for <?php echo htmlspecialchars($subject_name); ?> (Year: <?php echo $year; ?>, Section: <?php echo $section; ?>)</h2>

            <form action="" method="post" enctype="multipart/form-data">
                <input class="form-control" type="file" name="csv_file" accept=".csv" required>

                <div class="form-group mt-3">
                    <a class="btn btn-dark" href="teacher_dashboard.php">Back to Dashboard</a>
                    <button class="btn btn-danger btn-equal" type="submit">Upload Attendance</button>
                </div>
            </form>

            <h2 class="mt-3">Available Months</h2>
            <div class="container">
                <?php foreach ($months as $month) { ?>
                    <a href="?year=<?php echo $year; ?>&section=<?php echo $section; ?>&subject=<?php echo $subject_name; ?>&month=<?php echo $month; ?>" class="month-link">
                        <?php echo date('F Y', strtotime($month . '-01')); ?>
                    </a>
                    <?php if ($month !== end($months)) echo " | "; ?>
                <?php } ?>
            </div> <br>

            <h2>Imported Attendance Records</h2>
            <button class="btn btn-danger" onclick="printReport()">Print Attendance Report</button>
            <div id="printableTable">
                <table class="table mt-3">
                    <thead>
                        <tr>
                            <th>LRN</th>
                            <th>Name</th>
                            <?php
                            // Get the attendance dates for the selected month
                            $date_query = "SELECT DISTINCT date FROM attendance WHERE subject_id='$subject_id' AND teacher_id='$teacher_id' AND DATE_FORMAT(date, '%Y-%m')='$selected_month' ORDER BY date ASC";
                            $date_result = mysqli_query($conn, $date_query);
                            $dates = [];

                            while ($date_row = mysqli_fetch_assoc($date_result)) {
                                $date = $date_row['date'];
                                $dates[] = $date;
                                echo "<th>" . htmlspecialchars(date('m/d/Y', strtotime($date))) . " ";
                                echo "<button onclick=\"editDate('$date')\">Edit</button>";
                                echo " <button onclick=\"deleteDate('$date')\">Delete</button>";
                                echo "</th>";
                            }
                            ?>
                            <th>Present Total</th>
                            <th>Absent Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $student_query = "SELECT s.lrn, s.first_name, s.last_name FROM students s
                                        JOIN attendance a ON s.lrn = a.student_lrn
                                        WHERE a.subject_id='$subject_id' AND a.teacher_id='$teacher_id' AND DATE_FORMAT(a.date, '%Y-%m')='$selected_month'
                                        GROUP BY s.lrn ORDER BY s.last_name ASC";
                        $student_result = mysqli_query($conn, $student_query);

                        $total_present = 0;
                        $total_absent = 0;

                        while ($student = mysqli_fetch_assoc($student_result)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($student['lrn']) . "</td>";
                            echo "<td>" . htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) . "</td>";

                            $present_count = 0;
                            $absent_count = 0;

                            foreach ($dates as $date) {
                                $attendance_query = "SELECT status FROM attendance WHERE student_lrn='{$student['lrn']}' AND subject_id='$subject_id' AND date='$date'";
                                $attendance_result = mysqli_query($conn, $attendance_query);
                                $attendance = mysqli_fetch_assoc($attendance_result);
                                $status = $attendance ? $attendance['status'] : '';

                                if ($status === 'A') {
                                    echo "<td>A</td>";
                                    $absent_count++;
                                } else {
                                    echo "<td></td>";
                                    $present_count++;
                                }
                            }

                            echo "<td>$present_count</td>";
                            echo "<td>$absent_count</td>";
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
