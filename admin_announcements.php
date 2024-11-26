<?php
session_start();
include('db.php'); // Include your database connection

// Handle adding a new announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_announcement'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $audience = mysqli_real_escape_string($conn, $_POST['audience']);

    // Insert announcement into the database
    $query = "INSERT INTO announcements (title, content, audience) VALUES ('$title', '$content', '$audience')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Announcement posted successfully!');</script>";
    } else {
        echo "<script>alert('Error: Unable to post announcement.');</script>";
    }
}

// Handle updating an announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_announcement'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $audience = mysqli_real_escape_string($conn, $_POST['audience']);

    // Update announcement in the database
    $query = "UPDATE announcements SET title='$title', content='$content', audience='$audience' WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Announcement updated successfully!');</script>";
    } else {
        echo "<script>alert('Error: Unable to update announcement.');</script>";
    }
}

// Handle deleting an announcement
if (isset($_GET['delete_id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    
    // Delete the announcement from the database
    $query = "DELETE FROM announcements WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Announcement deleted successfully!'); window.location='admin_announcements.php';</script>";
    } else {
        echo "<script>alert('Error: Unable to delete announcement.');</script>";
    }
}

// Fetch announcement to edit if 'edit_id' is set
$edit_announcement = null;
if (isset($_GET['edit_id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['edit_id']);
    
    // Fetch the announcement from the database
    $query = "SELECT * FROM announcements WHERE id = $id";
    $result = mysqli_query($conn, $query);
    $edit_announcement = mysqli_fetch_assoc($result);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">  
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.bootstrap5.css">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <title>Admin Announcements</title>
    
</head>
<style>


    .admin-announcement-page {
        height: 100vh;
        background: url('images/bg.jpg') no-repeat center / cover;
    }

        .admin-announcement-page::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: inherit;
        filter: blur(10px);
        z-index: -1;
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
        overflow-y: scroll;
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

<div class="admin-announcement-page d-flex">
    <div class="sidebar bg-light shadow p-3">
        <div class="sidebar-title d-flex align-items-center">
            <img src="images/logo.png" alt="">
            <h5>Admin Dashboard</h5>
        </div>

        <div class="sidebar-menu bg-light mt-3">
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

      <!--Modal for change password-->

        <div class="modal bg-light" id="changePassword">
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

                                            <a class="btn btn-dark" href="<?php echo (isset($_SESSION['student_logged_in']) ? "student_dashboard.php" : (isset($_SESSION['parent_logged_in']) ? "parent_dashboard.php" : (isset($_SESSION['admin_logged_in']) ? "admin_announcements.php" : ""))); ?>">Cancel</a>
                                        </form>
                                        
                                    </div>
                                </div>
                            </div>
        </div>
        
    <div class="main-content flex-grow-1 h-100 p-3">

        <div class="container-fluid d-flex justify-content-between">
        <h2 class="text-white"><?php echo isset($edit_announcement) ? 'Edit Announcement' : 'Post Announcement'; ?></h2>
        <button class="navbar-toggler navbar-light burger" type="button" data-bs-toggle="offcanvas" data-bs-target="#demo" aria-controls="demo">
                <span class="navbar-toggler-icon"></span>
            </button>

                <!-- Offcanvas Sidebar -->
                <div class="offcanvas bg-light offcanvas-end" tabindex="-1" id="demo" aria-labelledby="offcanvasDemoLabel">
                    
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
            <div class="container-fluid bg-light shadow  p-5">
                <form method="POST" action="admin_announcements.php">
                        <input type="hidden" name="id" value="<?php echo isset($edit_announcement) ? $edit_announcement['id'] : ''; ?>">
                        
                        <label for="title" class="h3">Title:</label><br>
                        <input type="text" class="form-control" name="title" value="<?php echo isset($edit_announcement) ? $edit_announcement['title'] : ''; ?>" required><br>

                        <label for="content" class="h3">Content:</label><br>
                        <textarea name="content"class="form-control" rows="5" required><?php echo isset($edit_announcement) ? $edit_announcement['content'] : ''; ?></textarea><br>

                        <label for="audience" class="h3">Audience:</label><br>
                        <select name="audience" class="form-control" required>
                            <option value="students" <?php if (isset($edit_announcement) && $edit_announcement['audience'] == 'students') echo 'selected'; ?>>Students</option>
                            <option value="parents" <?php if (isset($edit_announcement) && $edit_announcement['audience'] == 'parents') echo 'selected'; ?>>Parents</option>
                            <option value="teachers" <?php if (isset($edit_announcement) && $edit_announcement['audience'] == 'teachers') echo 'selected'; ?>>Teachers</option>
                            <option value="all" <?php if (isset($edit_announcement) && $edit_announcement['audience'] == 'all') echo 'selected'; ?>>All</option>
                        </select> <br>

                        <button type="submit" class="btn btn-danger" name="<?php echo isset($edit_announcement) ? 'update_announcement' : 'add_announcement'; ?>">
                            <?php echo isset($edit_announcement) ? 'Update Announcement' : 'Post Announcement'; ?>
                        </button>
                </form>
            </div>

            <div class="container-fluid">
                <h2 class="mt-3 text-white">Manage Announcements</h2>
            </div>
        

            <div class="container-fluid bg-light shadow p-3">

                <!-- List of announcements with Edit and Delete options -->
                <?php
                // Fetch all announcements
                $query = "SELECT * FROM announcements ORDER BY date_posted DESC";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                    echo "<table class='table table-bordered table-striped' id='example'>";
                    echo "<thead><tr class='bg-danger text-light'><th>Title</th><th>Content</th><th>Audience</th><th>Date Posted</th><th>Actions</th></tr></thead>";
                    echo "<tbody>";

                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['title'] . "</td>";
                        echo "<td>" . $row['content'] . "</td>";
                        echo "<td>" . $row['audience'] . "</td>";
                        echo "<td>" . $row['date_posted'] . "</td>";
                        echo "<td>
                                <a class='btn btn-dark' href='admin_announcements.php?edit_id=" . $row['id'] . "'>Edit</a>
                                <a class='btn btn-danger' href='admin_announcements.php?delete_id=" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to delete this announcement?\");'>Delete</a>
                            </td>";
                        echo "</tr>";
                    }

                    echo "</tbody>";
                    echo "</table>";
                } else {
                    echo "No announcements found.";
                }
                ?>
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
        $(document).ready(function() {
            $('#example').DataTable({
                responsive: true
            });
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
