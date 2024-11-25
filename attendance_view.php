<?php
session_start();
include('db.php'); // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['student_logged_in']) && !isset($_SESSION['parent_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Determine if the logged-in user is a student or a parent
$user_id = isset($_SESSION['student_logged_in']) ? $_SESSION['student_username'] : $_SESSION['parent_username'];
$role = isset($_SESSION['student_logged_in']) ? 'student' : 'parent';

// Get student LRN or child's LRN (for parent)
$student_lrn = $role === 'student' ? $user_id : getChildLrn($user_id);

// Fetch subject from URL
$subject_name = isset($_GET['subject']) ? mysqli_real_escape_string($conn, $_GET['subject']) : '';

// Fetch subject ID based on the name provided
$subject_query = "SELECT id FROM subjects_sections WHERE subject_name='$subject_name'";
$subject_result = mysqli_query($conn, $subject_query);
$subject = mysqli_fetch_assoc($subject_result);
$subject_id = $subject ? $subject['id'] : null;

// Function to get child's LRN for parents
function getChildLrn($parent_username) {
    global $conn;
    $query = "SELECT lrn_students FROM parents WHERE username='$parent_username'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['lrn_students'];
}

// Get student details
$student_query = "SELECT first_name, last_name FROM students WHERE lrn='$student_lrn'";
$student_result = mysqli_query($conn, $student_query);
$student = mysqli_fetch_assoc($student_result);
$student_name = $student ? $student['first_name'] . " " . $student['last_name'] : '';

// Get the current month and year, defaulting to the current date
$month = isset($_GET['month']) ? $_GET['month'] : date('m');
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Calculate total days in the month
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

// Initialize totals for present and absent counts
$total_present = 0;
$total_absent = 0;

// Display attendance records for the specified subject
if ($subject_id) {
    // Get first and last dates of the month
    $startDate = "$year-$month-01";
    $endDate = "$year-$month-$daysInMonth";

    // Calculate total counts for present and absent days in this month
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $date = "$year-$month-" . str_pad($day, 2, '0', STR_PAD_LEFT);

        // Fetch attendance status for each date
        $attendance_query = "SELECT status FROM attendance WHERE student_lrn='$student_lrn' AND subject_id='$subject_id' AND date='$date'";
        $attendance_result = mysqli_query($conn, $attendance_query);
        $attendance = mysqli_fetch_assoc($attendance_result);

        // Count totals for present and absent days
        if ($attendance) {
            if ($attendance['status'] === 'A') {
                $total_absent++;
            } else {
                $total_present++;
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Calendar View</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        .attendance-view {
            background: url(images/bg.jpg);
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }
        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
            max-width: 600px;
            margin: auto;
        }
        .day {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            height: 50px;
            font-size: 14px;
            color: #333;
        }
        .day.present { background-color: #c8e6c9; } /* Green for present */
        .day.absent { background-color: #ffcdd2; } /* Red for absent */
        .calendar-nav {
            display: flex;
            justify-content: space-between;
            max-width: 600px;
            margin: auto;
            padding: 10px 0;
        }
        @media print {
            .calendar-nav, .calendar { display: none; }
            .attendance-report { display: block; }
        }
        .attendance-report {
            margin: 20px;
        }
    </style>
    <script>
        function printAttendanceReport() {
            // Show the printable report
            var printSection = document.getElementById('attendance-report').innerHTML;
            var originalContent = document.body.innerHTML;

            // Hide all other content and show only the attendance report
            document.body.innerHTML = printSection;

            // Trigger print dialog
            window.print();

            // Restore the original content after printing
            document.body.innerHTML = originalContent;
        }
    </script>
</head>

<body class="attendance-view">

   
<div class="container p-5 d-flex justify-content-center align-items-center">

   



    <div class="container bg-light shadow p-5">
    <h3><?php echo htmlspecialchars($subject_name); ?> Attendance Records</h3>

        <div class="calendar-nav">
            <a class="btn btn-dark shadow" href="?subject=<?php echo htmlspecialchars($subject_name); ?>&month=<?php echo $month == 1 ? 12 : $month - 1; ?>&year=<?php echo $month == 1 ? $year - 1 : $year; ?>">&#8592; Previous Month</a>
            <h3><?php echo date('F Y', strtotime("$year-$month-01")); ?></h3>
            <a class="btn btn-danger shadow" href="?subject=<?php echo htmlspecialchars($subject_name); ?>&month=<?php echo $month == 12 ? 1 : $month + 1; ?>&year=<?php echo $month == 12 ? $year + 1 : $year; ?>">Next Month &#8594;</a>
        </div>
        <div class="calendar shadow">
            <?php
            $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            foreach ($daysOfWeek as $day) {
                echo "<div class='day'><strong>$day</strong></div>";
            }

            $firstDayOfMonth = date('w', strtotime("$year-$month-01"));
            for ($i = 0; $i < $firstDayOfMonth; $i++) {
                echo "<div class='day'></div>"; // Empty days before the start of the month
            }

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = "$year-$month-" . str_pad($day, 2, '0', STR_PAD_LEFT);
                $attendance_query = "SELECT status FROM attendance WHERE student_lrn='$student_lrn' AND subject_id='$subject_id' AND date='$date'";
                $attendance_result = mysqli_query($conn, $attendance_query);
                $attendance = mysqli_fetch_assoc($attendance_result);

                $status_class = $attendance ? ($attendance['status'] === 'A' ? 'absent' : 'present') : '';
                echo "<div class='day $status_class'>$day</div>";
            }
            ?>
        </div>
        <div class="calendar-content shadow mt-5">
            <table class="table table-bordered">
                <thead class="bg-danger text-light">
                    <tr>
                        <th>Student Name</th>
                        <th>Total Present</th>
                        <th>Total Absent</th>
                    </tr>
                </thead>
                <tr>
                    <td><?php echo htmlspecialchars($student_name); ?></td>
                    <td><?php echo $total_present; ?></td>
                    <td><?php echo $total_absent; ?></td>
                </tr>
            </table>
        </div>
        <div class="container-fluid d-flex justify-content-end" style="gap: 5px; flex-wrap: wrap;">

            <?php 
                switch ($role) {
                    case 'student':
                        $dashboard_url = 'student_dashboard.php';
                        break;
                    case 'parent':
                        $dashboard_url = 'parent_dashboard.php';
                        break;
                    default:
                        $dashboard_url = '';
                        break;
                }
                if ($dashboard_url): ?>
                    <a class="btn btn-dark" href="<?php echo $dashboard_url; ?>">Back to dashboard</a>
            <?php endif; ?>
        
            <button class="btn btn-danger shadow" onclick="printAttendanceReport()">Print Report</button>
        </div>


    </div>
    

    <!-- Print-friendly attendance report (hidden on page) -->
    <div id="attendance-report" class="attendance-report" style="display:none;">
        <h3 class="text-center"> Attendance Report for <?php echo date('F Y', strtotime("$year-$month-01")); ?></h3>
        <table class="table mt-5" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Loop through the days of the month and show only present/absent days
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = "$year-$month-" . str_pad($day, 2, '0', STR_PAD_LEFT);
                    $attendance_query = "SELECT status FROM attendance WHERE student_lrn='$student_lrn' AND subject_id='$subject_id' AND date='$date'";
                    $attendance_result = mysqli_query($conn, $attendance_query);
                    $attendance = mysqli_fetch_assoc($attendance_result);

                    // Show the date only if the student was present or absent
                    if ($attendance) {
                        $status = $attendance['status'] === 'A' ? 'Absent' : 'Present';
                        echo "<tr><td>" . date('l, F d, Y', strtotime($date)) . "</td><td>$status</td></tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    </div>

</body>
</html>





<?php
} else {
    echo "<p>No records found for this subject.</p>";
}
?>
