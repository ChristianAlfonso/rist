<?php
session_start();
include('db.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Fetch student details
if (isset($_GET['lrn'])) {
    $lrn = $_GET['lrn'];

    // Validate and sanitize the input
    $lrn = htmlspecialchars($lrn);

    // Prepare statement to fetch student details
    $stmt = $conn->prepare("SELECT * FROM students WHERE lrn = ?");
    $stmt->bind_param("s", $lrn);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if student exists
    if ($result->num_rows === 0) {
        echo "Student not found.";
        exit();
    }

    $student = $result->fetch_assoc();
} else {
    echo "Invalid request.";
    exit();
}

// Update student details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $middle_initial = htmlspecialchars($_POST['middle_initial']);
    $email = htmlspecialchars($_POST['email']);
    $year = htmlspecialchars($_POST['year']);
    $section = htmlspecialchars($_POST['section']);

    // Prepare statement to update student details
    $update_stmt = $conn->prepare("UPDATE students SET first_name = ?, last_name = ?, middle_initial = ?, email = ?, year = ?, section = ? WHERE lrn = ?");
    $update_stmt->bind_param("ssssssi", $first_name, $last_name, $middle_initial, $email, $year, $section, $lrn);
    if ($update_stmt->execute()) {
        header("Location: users_students.php"); // Redirect to students list
        exit();
    } else {
        echo "Error updating student details.";
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
    <title>Edit Student</title>
</head>
<style>

    .admin-edit-student {
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

<div class="admin-edit-student vh-100 d-flex">
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
    <h2>Edit Student</h2>
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
        <form method="POST">

            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input class="form-control" type="text" name="first_name" value="<?php echo htmlspecialchars($student['first_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input class="form-control" type="text" name="last_name" value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="middle_initial">Middle Initial:</label>
                <input class="form-control" type="text" name="middle_initial" value="<?php echo htmlspecialchars($student['middle_initial']); ?>" maxlength="1">
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input class="form-control" type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
            </div>
    
            <div class="form-group">
                <label for="year">Year:</label>
                <input class="form-control" type="text" name="year" value="<?php echo htmlspecialchars($student['year']); ?>" required>
            </div>

            <div class="form-group">
                <label for="section">Section:</label>
                <input class="form-control" type="text" name="section" value="<?php echo htmlspecialchars($student['section']); ?>" required>
            </div>

            <div class="form-group mt-3">
                <button class="btn btn-danger" type="submit">Update Student</button>
                <a class="btn btn-dark" href="users_students.php">Cancel</a>

            </div>

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

