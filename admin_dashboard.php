<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

?>



<!DOCTYPE html>
<html lang="en">
    <style>

    </style>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Admin Dashboard</title>
</head>
<body>
      <!--Header-->
      <div class="">
        <header class="header">
            <nav class="nav">
            <img src="images/logo1.jfif" alt="Logo" class="logo">
              <span class="logo_text"><h1>Welcome! Admin</h1></span>
            </nav>
          </header>
    
    <h2>Register Users</h2>
    <ul>
        <li><a href="register_student.php">Register Students</a></li>
        <li><a href="import_students.php">Import Students (Excel)</a></li>
        <li><a href="register_teacher.php">Register Teachers</a></li>
        <li><a href="import_teachers.php">Import Teachers (Excel)</a></li>
        <li><a href="register_parent.php">Register Parents</a></li>
        <li><a href="import_parents.php">Import Parents(Excel)</a></li>

    </ul>

    <h2>Manage Users</h2>
<ul>
    <li><a href="users_students.php">View Students</a></li>
    <li><a href="users_teachers.php">View Teachers</a></li>
    <li><a href="users_parents.php">View Parents</a></li>
</ul>

    
    <h3>Announcements</h3>
    <li><a href="admin_announcements.php">Announcements</a></li>

    <h2>Logout & Change Password</h2>
    <li><a href="logout.php">Logout</a></li>
    <li><a href="change_password.php">Change Password</a></li>
</body>
</html>
