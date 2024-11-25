<?php
session_start();
include('db.php');

// add admin at inayos ko lang yung sa admin
if (!isset($_SESSION['student_logged_in']) && 
    !isset($_SESSION['parent_logged_in']) && 
    !isset($_SESSION['teacher_logged_in']) && 
    !isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = mysqli_real_escape_string($conn, $_POST['current_password']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    if ($new_password === $confirm_password) {
        
        if (isset($_SESSION['student_logged_in'])) {
            $username = $_SESSION['student_username'];
            $query = "SELECT * FROM students WHERE username = '$username'";
        } elseif (isset($_SESSION['parent_logged_in'])) {
            $username = $_SESSION['parent_username'];
            $query = "SELECT * FROM parents WHERE username = '$username'";
        } elseif (isset($_SESSION['teacher_logged_in'])) {
            $username = $_SESSION['teacher_username'];
            $query = "SELECT * FROM teachers WHERE username = '$username'";
        } elseif (isset($_SESSION['admin_logged_in'])) {
            $username = $_SESSION['admin_username'];
            $query = "SELECT * FROM admin WHERE username = '$username'";
        }

        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

           
            if (password_verify($current_password, $user['password'])) {
                
                $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);

                $update_query = "UPDATE " . 
                                (isset($_SESSION['student_logged_in']) ? "students" : 
                                (isset($_SESSION['parent_logged_in']) ? "parents" : 
                                (isset($_SESSION['teacher_logged_in']) ? "teachers" : "admin"))) . 
                                " SET password='$new_password_hashed' WHERE username='$username'";

                if (mysqli_query($conn, $update_query)) {
                    if(isset($_SESSION['admin_logged_in'])){
                        header("Location:admin_announcements.php");
                    }
                    else if(isset($_SESSION['student_logged_in'])){
                        header("Location:student_dashboard.php");
                    }
                    else if(isset($_SESSION['parent_logged_in'])){
                        header("Location:parent_dashboard.php");
                    }
                    else if(isset($_SESSION['teacher_logged_in'])){
                        header("Location:teacher_dashboard.php");
                    }
                } else {
                    echo "<script>alert('Error updating password.');</script>";
                    header("Location:admin_announcements.php");
                }

            } else {
                echo "<script>alert('Current password is incorrect.');</script>";
                header("Location:admin_announcements.php");
                
            }
        } else {
            echo "<script>alert('User not found.');</script>";
            header("Location:admin_announcements.php");
            
        }
    } else {
        echo "<script>alert('New passwords do not match.');</script>";
        header("Location:admin_announcements.php");
        
    }
}
?>
