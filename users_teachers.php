<?php
session_start();
include('db.php');

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Fetch teachers from the database
$teachers_query = "SELECT * FROM teachers";
$teachers_result = mysqli_query($conn, $teachers_query);

// Handle teacher deletion
if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);

    // Check if the sections table exists
    $check_sections_table_query = "SHOW TABLES LIKE 'sections'";
    $sections_table_exists = mysqli_query($conn, $check_sections_table_query);

    if (mysqli_num_rows($sections_table_exists) > 0) {
        // First, delete any sections associated with the teacher
        $delete_sections_query = "DELETE FROM sections WHERE teacher_id = '$delete_id'";
        mysqli_query($conn, $delete_sections_query); // Ignore errors for sections
    }

    // Then, delete the teacher
    $delete_query = "DELETE FROM teachers WHERE id = '$delete_id'";
    if (mysqli_query($conn, $delete_query)) {
        echo "<script>alert('Teacher and their sections deleted successfully!'); window.location='users_teachers.php';</script>";
    } else {
        echo "<script>alert('Error deleting teacher');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teachers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">  
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.bootstrap5.css">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<style>

    .admin-user-teacher {
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


    <div class="admin-user-teacher d-flex">
        
        <div class="sidebar shadow p-3" >
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

        <div class="main-content flex-grow-1 p-3 w-100">
            <div class="container-fluid d-flex justify-content-between">
            <h1>Registered Teachers</h1>
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


            <div class="container-fluid shadow p-3">
                    <table class="table table-striped table-bordered nowrap" id="example">
                        <thead>
                            <tr>
                                <th>Teacher ID</th>
                                <th>Subject</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($teachers_result)) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['subject']); ?></td>
                                    <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td>
                                        <a class="btn btn-dark" href="edit_teacher.php?id=<?php echo urlencode($row['id']); ?>">Edit</a> 
                                        <a class="btn btn-danger" href="users_teachers.php?delete_id=<?php echo urlencode($row['id']); ?>" onclick="return confirm('Are you sure you want to delete this teacher and their associated sections?');">Delete</a>
                                    </td>
                                </tr>
                            <?php } ?>
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
