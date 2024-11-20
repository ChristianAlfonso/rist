<?php
include('db.php');

if (isset($_GET['old_date']) && isset($_GET['new_date']) && isset($_GET['subject_id'])) {
    $old_date = mysqli_real_escape_string($conn, $_GET['old_date']);
    $new_date = mysqli_real_escape_string($conn, $_GET['new_date']);
    $subject_id = mysqli_real_escape_string($conn, $_GET['subject_id']);

    $query = "UPDATE attendance SET date='$new_date' WHERE date='$old_date' AND subject_id='$subject_id'";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Date updated successfully.'); window.location.href = 'attendance.php?subject_id=$subject_id';</script>";
    } else {
        echo "<script>alert('Error updating date.'); window.location.href = 'attendance.php?subject_id=$subject_id';</script>";
    }
}
?>
