<?php
session_start();
include('db.php');

// Check if the parent is logged in
if (!isset($_SESSION['parent_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Fetch parent details
$username = $_SESSION['parent_username'];
$query = $conn->prepare("SELECT * FROM parents WHERE username = ?");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();
$parent = $result->fetch_assoc();

if ($parent) {
    $lrn = $parent['lrn_students'];
} else {
    echo "Parent not found.";
    exit();
}

// Fetch student's details linked to the parent
$query = $conn->prepare("SELECT * FROM students WHERE lrn = ?");
$query->bind_param("s", $lrn);
$query->execute();
$result = $query->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    echo "Student not found for this parent.";
    exit();
}

// Fetch year levels for the student
$year_levels_query = "SELECT DISTINCT year_level FROM subjects_sections ORDER BY year_level ASC";
$year_levels_result = mysqli_query($conn, $year_levels_query);
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
    
    <title>Parent Dashboard</title>
</head>

<style>

    .parent-dashboard-parent {
        width: 100vw;
    }

   

    .nav-link:hover{
        background-color: #f6ded7;
        color: #982718;
    }
    
    .nav-item a {
        text-decoration: none;
        color: #982718;
        font-weight: bold;
    }
    
    .nav-link.active {
        background-color: #982718 !important;
        color: #fff !important;
    }

    img {
        width: 80px;
    }


    .burger {
        display: none;
    }
  
    
</style>
<body>

<div class="teacher-dashboard-parent">
    
        <div class="navbar navbar-expand-md navbar-light shadow bg-light p-3">
            <div class="container">

                <div class="navbar-title d-flex align-items-center">
                    <img src="images/logo.png" alt="">
                </div>

                <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasSidebar">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title">Menu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                    </div>
                    <div class="offcanvas-body">
                        <ul class="nav nav-pills flex-column">
                            <li class="nav-item">
                                <a href="#information" data-bs-toggle="tab" class="nav-link active">Information</a>
                            </li>
                            <li class="nav-item">
                                <a href="#announcement" data-bs-toggle="tab" class="nav-link">Announcement</a>
                            </li>
                            <li class="nav-item">
                                <a href="#feedback" data-bs-toggle="tab" class="nav-link">Feedback</a>
                            </li>
                            <li class="nav-item">
                                <a href="login.php" class="nav-link">Logout</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <ul class="nav nav-pills ms-auto d-none d-md-flex">
                    <li class="nav-item">
                        <a href="#information" data-bs-toggle="tab" class="nav-link active">Information</a>
                    </li>
                    <li class="nav-item">
                        <a href="#announcement" data-bs-toggle="tab" class="nav-link">Announcement</a>
                    </li>
                    <li class="nav-item">
                        <a href="#feedback" data-bs-toggle="tab" class="nav-link">Feedback</a>
                    </li>
                    <li class="nav-item">
                        <a href="login.php" class="nav-link">Logout</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="tab-content">
            <div class="tab-pane container mt-5 active" id="information">
                  <h1>Welcome, <?php echo htmlspecialchars($parent['first_name']); ?>!</h1>

                    <div class="container-fluid shadow p-5">
                        <h2>Your Information</h2>
                        <p><strong>Student LRN:</strong> <?php echo htmlspecialchars($lrn); ?></p>
                        <p><strong>Full Name:</strong> <?php echo htmlspecialchars($parent['first_name'] . ' ' . $parent['last_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($parent['email']); ?></p>

                        <div class="container-fluid d-flex justify-content-end">
                            <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#changePassword">Change Password</button>

                            <!--Modal for change password-->

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

                                                <a class="btn btn-dark" href="<?php echo (isset($_SESSION['student_logged_in']) ? "student_dashboard.php" : (isset($_SESSION['parent_logged_in']) ? "parent_dashboard.php" : "teacher_dashboard.php")); ?>">Cancel</a>
                                            </form>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>

                        
                        </div>

                    </div>

                    <div class="container shadow p-5">
                        <h2>Your Student Year Levels</h2>
    
                            <?php if (mysqli_num_rows($year_levels_result) > 0): ?>
                                <table class="table table-bordered" id="example">
                                    <thead>
                                        <tr>
                                            <th>Year Level</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($year_level = mysqli_fetch_assoc($year_levels_result)): ?>
                                            <tr>
                                                <td><?php echo "Year " . htmlspecialchars($year_level['year_level']); ?></td>
                                                <td>
                                                    <a href="subjects_view.php?year_level=<?php echo urlencode($year_level['year_level']); ?>" class="btn btn-danger">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p>No year levels available for your child.</p>
                            <?php endif; ?>
                    </div>


            </div>



            <div class="tab-pane container mt-5 fade" id="announcement">
                <div class="container-fluid shadow p-5">
                    <!-- Announcements Section -->
                        <h2>Announcements</h2>
                        <?php
                        $announcements_query = "SELECT * FROM announcements WHERE audience IN ('parents', 'all') ORDER BY date_posted DESC";
                        $announcements_result = mysqli_query($conn, $announcements_query);
                        if (mysqli_num_rows($announcements_result) > 0): ?>
                            <ul>
                                <?php while ($announcement = mysqli_fetch_assoc($announcements_result)): ?>
                                    <li>
                                        <h3><?php echo htmlspecialchars($announcement['title']); ?></h3>
                                        <p><?php echo htmlspecialchars($announcement['content']); ?></p>
                                        <small>Posted on: <?php echo date('F j, Y', strtotime($announcement['date_posted'])); ?></small>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        <?php else: ?>
                            <p>No announcements at the moment.</p>
                        <?php endif; ?>
                </div>
            </div>


            <div class="tab-pane container mt-5 fade" id="feedback">
                                
                    <h2>Feedback</h2>
                    <ul>
                    <li><a href="feedback_view.php">View Feedback</a></li>
                    </ul>

   
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
