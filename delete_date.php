<?php
include('db.php');

if (isset($_GET['date']) && isset($_GET['subject_id'])) {
    $date = mysqli_real_escape_string($conn, $_GET['date']);
    $subject_id = mysqli_real_escape_string($conn, $_GET['subject_id']);

    $query = "DELETE FROM attendance WHERE date='$date' AND subject_id='$subject_id'";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Date deleted successfully.'); window.location.href = 'attendance.php?subject_id=$subject_id';</script>";
    } else {
        echo "<script>alert('Error deleting date.'); window.location.href = 'attendance.php?subject_id=$subject_id';</script>";
    }
}
?>
