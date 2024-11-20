<?php
session_start();
include('db.php');

// Check if the teacher is logged in
if (!isset($_SESSION['teacher_logged_in'])) {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_username'];
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
                 ORDER BY s.last_name, s.first_name";
$grades_result = mysqli_query($conn, $grades_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Grades for <?php echo htmlspecialchars($subject_name); ?> (Year: <?php echo $year; ?>, Section: <?php echo $section; ?>, Quarter: <?php echo $quarter; ?>)</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <h1>Upload or Update Grades for <?php echo htmlspecialchars($subject_name); ?> (Year: <?php echo $year; ?>, Section: <?php echo $section; ?>)</h1>

    <form action="" method="post" enctype="multipart/form-data">
        <!-- Dropdown for selecting the quarter -->
        <label for="quarter">Select Quarter:</label>
        <select name="quarter" id="quarter" required onchange="this.form.submit()">
            <option value="1st" <?php echo $quarter === '1st' ? 'selected' : ''; ?>>1st Quarter</option>
            <option value="2nd" <?php echo $quarter === '2nd' ? 'selected' : ''; ?>>2nd Quarter</option>
            <option value="3rd" <?php echo $quarter === '3rd' ? 'selected' : ''; ?>>3rd Quarter</option>
            <option value="4th" <?php echo $quarter === '4th' ? 'selected' : ''; ?>>4th Quarter</option>
        </select>
        <br>

        <label for="csv_file">Upload Grades (CSV):</label>
        <input type="file" name="csv_file" id="csv_file" accept=".csv" required>
        <br>

        <input type="submit" value="Upload Grades">
    </form>

    <h2>Uploaded Grades for <?php echo $quarter; ?> Quarter</h2>
    <table>
        <thead>
            <tr>
                <th>LRN</th>
                <th>Name</th>
                <?php
                // Fetch a row to check the scores structure
                $sample_row = mysqli_fetch_assoc($grades_result);
                if ($sample_row) {
                    // Attempt to decode the scores and check if it's valid
                    $sample_scores = json_decode($sample_row['scores'], true);
                    if (is_array($sample_scores)) {
                        foreach ($sample_scores as $key => $value) {
                            echo "<th>" . htmlspecialchars($key) . "</th>";
                        }
                    } else {
                        // Handle invalid scores or empty case by skipping or using default column names
                        echo "<th>No scores available</th>";
                    }
                }
                ?>
            </tr>
        </thead>
        <tbody>
            <?php
            // Reset pointer to the result set and display all grades
            mysqli_data_seek($grades_result, 0);
            while ($row = mysqli_fetch_assoc($grades_result)) {
                // Attempt to decode the scores safely
                $scores = json_decode($row['scores'], true);
                if (!is_array($scores)) {
                    // If decoding fails, set $scores to an empty array or default values
                    $scores = ['No scores available'];
                }
                
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['student_lrn']) . "</td>";
                echo "<td>" . htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) . "</td>";
                foreach ($scores as $score) {
                    echo "<td>" . htmlspecialchars($score) . "</td>";
                }
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

</body>
</html>
