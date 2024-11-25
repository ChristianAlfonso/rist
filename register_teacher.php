<?php
include('db.php'); // Include your database connection

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id'],$_POST['subject'], $_POST['last_name'], $_POST['first_name'], $_POST['middle_initial'], $_POST['email'], $_POST['username'], $_POST['password'])) {

        // Sanitize and hash input values
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $subject = mysqli_real_escape_string($conn, $_POST['subject']);
        $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
        $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
        $middle_initial = mysqli_real_escape_string($conn, $_POST['middle_initial']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = password_hash(mysqli_real_escape_string($conn, $_POST['password']), PASSWORD_DEFAULT);

        // Insert teacher data
        $query = "INSERT INTO teachers (id, subject, last_name, first_name, middle_initial, email, username, password) 
                  VALUES ('$id', '$subject', '$last_name', '$first_name', '$middle_initial', '$email', '$username', '$password')";
        
       
            echo "<script>alert('Teacher registered successfully!'); window.location='users_teachers.php';</script>";
       
    } else {
        echo "<script>alert('Error: Please fill out all required fields.');</script>";
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
    <title>Register Teacher</title>
   
</head>


<style>
    .admin-register-teacher {
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

        .main-content {
            overflow-y: scroll
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

    <div class="admin-register-teacher d-flex">
        <div class="sidebar shadow p-3">
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
                        <a data-bs-toggle="modal" data-bs-target="#changePassword" href="change_password.php">Change Password</a>
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

        <div class="modal" id="changePassword">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Change Password</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="change_password.php">
                                            <label for="current_password">Current Password:</label><br>
                                            <input class="form-control" type="password" name="current_password" required><br>

                                            <label for="new_password">New Password:</label><br>
                                            <input class="form-control" type="password" name="new_password" required><br>

                                            <label for="confirm_password">Confirm New Password:</label><br>
                                            <input class="form-control" type="password" name="confirm_password" required><br><br>

                                            <button class="btn btn-danger" type="submit">Change Password</button>

                                            <a class="btn btn-dark" href="<?php echo (isset($_SESSION['student_logged_in']) ? "student_dashboard.php" : (isset($_SESSION['parent_logged_in']) ? "parent_dashboard.php" : (isset($_SESSION['admin_logged_in']) ? "register_teacher.php" : ""))); ?>">Cancel</a>
                                        </form>
                                        
                                    </div>
                                </div>
                            </div>
        </div>

        <div class="main-content flex-grow-1 h-100 p-3">
            <div class="container-fluid d-flex justify-content-between">
                <h2>Register Teacher</h2>
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


        <div class="container-fluid shadow p-4">
            <form method="POST" action="register_teacher.php">

                    <div class="form-group">
                        <label for="id">Teacher ID:</label>
                        <input type="text" name="id" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="subject">Subject:</label>
                        <input type="text" name="subject" class="form-control" required>
                    </div>

                    <div class="row">

                        <div class="form-group col-sm col-md-4">
                            <label for="last_name">Last Name:</label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                        
                        <div class="form-group col-sm col-md-4">
                            <label for="first_name">First Name:</label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>

                        <div class="form-group col-sm col-md-4">
                            <label for="middle_initial">Middle Initial:</label>
                            <input type="text" name="middle_initial" class="form-control" required>
                        </div>

                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-danger mt-2">Register</button>
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
