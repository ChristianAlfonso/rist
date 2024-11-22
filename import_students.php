<?php
session_start();
include('db.php'); // Include your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['student_file'])) {
    $file = $_FILES['student_file']['tmp_name'];

    // Open and read the CSV file
    if (($handle = fopen($file, "r")) !== FALSE) {
        // Skip the first row (headers)
        $header = fgetcsv($handle, 1000, ",");

        // Process each row
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Extract student details from CSV
            $lrn = mysqli_real_escape_string($conn, $data[0]);
            $last_name = mysqli_real_escape_string($conn, $data[1]);
            $first_name = mysqli_real_escape_string($conn, $data[2]);
            $middle_initial = mysqli_real_escape_string($conn, $data[3]);
            $email = mysqli_real_escape_string($conn, $data[4]);
            $year = mysqli_real_escape_string($conn, $data[5]);
            $section = mysqli_real_escape_string($conn, $data[6]);
            $username = mysqli_real_escape_string($conn, $data[7]);
            $password = password_hash(mysqli_real_escape_string($conn, $data[8]), PASSWORD_DEFAULT);

            // Insert into the database
            $query = "INSERT INTO students (lrn, last_name, first_name, middle_initial, email, year, section, username, password) 
                      VALUES ('$lrn', '$last_name', '$first_name', '$middle_initial', '$email', '$year', '$section', '$username', '$password')";

            mysqli_query($conn, $query);
        }
        fclose($handle);
        echo "<script>alert('Students imported successfully!'); window.location='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error: Unable to open file.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Import Students (CSV)</title>
</head>
<style>

    .admin-import-student {
        height: 100vh;
        width: 100vw;
    }

    .nav-item {
        width: 100%;
        padding: 10px;
    }

    .nav-item:hover{
        background-color: #f6ded7;
    }
    .nav-item a {
        text-decoration: none;
        color: #982718;
        font-weight: bold;
    }
    

    img {
        width: 50px;
    }

    .sidebar {
        width: 300px;
    }

    .burger {
        display: none;
    }
    
    @media (max-width: 700px) {
        .sidebar {
            display: none;
        }

        .burger {
            display: block;
        }
    }
    
</style>
<body>

<div class="admin-import-student vh-100 d-flex">
    <div class="sidebar h-100 shadow p-3">
        <div class="sidebar-title d-flex align-items-center">
            <img src="images/logo.png" alt="">
            <h5>Admin Dashboard</h5>
        </div>

        <div class="sidebar-menu mt-3">
            <ul class="nav">
                <li class="nav-item">
                    <a href="admin_announcements.php">Announcements</a>
                </li>

                <li class="nav-item">
                    <a href="change_password.php">Change Password</a>
                </li>

                <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Students
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item" href="register_student.php">Register Students</a></li>
                            <li><a class="dropdown-item" href="import_students.php">Import Students</a></li>
                            <li><a class="dropdown-item" href="users_students.php">View Students</a></li>
                        </ul>
                </li>

                <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Teachers
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item" href="register_teacher.php">Register Teachers</a></li>
                            <li><a class="dropdown-item" href="import_teachers.php">Import Teachers</a></li>
                            <li><a class="dropdown-item" href="users_teachers.php">View Teachers</a></li>
                        </ul>
                </li>

                <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Parents
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item" href="register_parent.php">Register Parents</a></li>
                            <li><a class="dropdown-item" href="import_parents.php">Import Parents</a></li>
                            <li><a class="dropdown-item" href="users_parents.php">View Parents</a></li>
                        </ul>
                </li>

                <li class="nav-item">
                    <a href="logout.php">Logout</a>
                </li>



            </ul>
        </div>
    </div>

    <div class="main-content flex-grow-1 p-3">
    <div class="container-fluid d-flex justify-content-between">
    <h2>Import Students (CSV)</h2>
    <button class="navbar-toggler navbar-light burger" type="button" data-bs-toggle="offcanvas" data-bs-target="#demo" aria-controls="demo">
                <span class="navbar-toggler-icon"></span>
            </button>

                <!-- Offcanvas Sidebar -->
                <div class="offcanvas offcanvas-end" tabindex="-1" id="demo" aria-labelledby="offcanvasDemoLabel">
                    
                        <div class="offcanvas-header">
                            <h5 class="offcanvas-title" id="offcanvasDemoLabel">Sidebar Menu</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>

                        <div class="offcanvas-body">
                            <ul class="nav">
                                <li class="nav-item">
                                    <a href="admin_announcements.php">Announcements</a>
                                </li>
                                <li class="nav-item">
                                    <a href="change_password.php">Change Password</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="offcanvasStudentsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Students
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="offcanvasStudentsDropdown">
                                        <li><a class="dropdown-item" href="register_student.php">Register Students</a></li>
                                        <li><a class="dropdown-item" href="import_students.php">Import Students</a></li>
                                        <li><a class="dropdown-item" href="users_students.php">View Students</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="offcanvasTeachersDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Teachers
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="offcanvasTeachersDropdown">
                                        <li><a class="dropdown-item" href="register_teacher.php">Register Teachers</a></li>
                                        <li><a class="dropdown-item" href="import_teachers.php">Import Teachers</a></li>
                                        <li><a class="dropdown-item" href="users_teachers.php">View Teachers</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="offcanvasParentsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Parents
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="offcanvasParentsDropdown">
                                        <li><a class="dropdown-item" href="register_parent.php">Register Parents</a></li>
                                        <li><a class="dropdown-item" href="import_parents.php">Import Parents</a></li>
                                        <li><a class="dropdown-item" href="users_parents.php">View Parents</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a href="logout.php">Logout</a>
                                </li>
                            </ul>
                        </div>
                </div>
        </div>

        <div class="container shadow p-5">
                <form method="POST" enctype="multipart/form-data" action="import_students.php">
                    <label for="student_file">Upload CSV File:</label><br>
                    <input type="file" class="form-control" name="student_file" accept=".csv" required>
                    <button type="submit" class="btn btn-danger mt-3">Import</button>
                </form>
        </div>
    </div>
</div>






   
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <script>
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
