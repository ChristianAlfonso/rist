<?php
session_start();
include('db.php');


if (!isset($_SESSION['teacher_logged_in'])) {
    header("Location: login.php");
    exit();
}


$username = $_SESSION['teacher_username'];
$query = $conn->prepare("SELECT id, subject, first_name, last_name, email FROM teachers WHERE username = ?");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();
$teacher = $result->fetch_assoc();
$teacher_id = $teacher['id']; 


$announcement_query = $conn->prepare("SELECT * FROM announcements ORDER BY date_posted DESC");
$announcement_query->execute();
$announcements_result = $announcement_query->get_result();

// add school year
$subjects_query = $conn->prepare("SELECT id, subject_name, year_level, section, school_year FROM subjects_sections WHERE teacher_id = ?");

$subjects_query->bind_param("s", $teacher_id);
$subjects_query->execute();
$subjects_result = $subjects_query->get_result();
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
    
    <title>Teacher Dashboard</title>
</head>    

<style>

    .teacher-dashboard-parent {
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
    

    <div class="main-content container p-3">
    
        <div class="tab-content">

            <div class="tab-pane fade container-fluid" id="announcement">

                <div class="container-fluid shadow p-5">
                        <!-- Announcements Section -->
                    <h2>Announcements</h2>
                        <?php if (mysqli_num_rows($announcements_result) > 0): ?>
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

            <div class="tab-pane active container-fluid" id="information">
                
            <h1 class="mt-3">Welcome, <?php echo htmlspecialchars($teacher['first_name']); ?>!</h1>


                <div class="container-fluid shadow mt-3 p-5">
                    <h2>Your Information</h2>
                        <p><strong>Teacher ID:</strong> <?php echo htmlspecialchars($teacher['id']); ?></p>
                        <p><strong>Subject:</strong> <?php echo htmlspecialchars($teacher['subject']); ?></p>
                        <p><strong>Full Name:</strong> <?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($teacher['email']); ?></p>

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

                <div class="container-fluid shadow p-5 mt-3">
                    <!-- Added Subjects and Sections -->
                    <h2>Subjects and Sections</h2>
                    <?php if ($subjects_result->num_rows > 0): ?>
                        <table class="table table-striped table-bordered nowrap" id="example">
                            <thead>
                                <tr>
                                    <th>Year Level</th>
                                    <th>Section</th>
                                    <th>Subject Name</th>
                                    <th>School Year</th> <!-- Added this line -->
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($subject = $subjects_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($subject['year_level']); ?></td>
                                        <td><?php echo htmlspecialchars($subject['section']); ?></td>
                                        <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                        <td><?php echo htmlspecialchars($subject['school_year']); ?></td> <!-- Added this line -->
                                        <td>
                                            <a class="btn btn-dark" href="?delete_id=<?php echo urlencode($subject['id']); ?>" onclick="return confirm('Are you sure you want to delete this subject/section?');">Delete</a>
                                            <a class="btn btn-danger" href="view_subject_details.php?year=<?php echo urlencode($subject['year_level']); ?>&section=<?php echo urlencode($subject['section']); ?>&subject=<?php echo urlencode($subject['subject_name']); ?>">View</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No subjects added yet.</p>
                    <?php endif; ?>
                    
                    <div class="container-fluid d-flex justify-content-end mt-3">
                        <a href="add_subject_section.php" class="btn btn-danger">Add Subject/Section</a>
                    </div>
                </div>

            </div>

            <div class="tab-pane fade-container-fluid" id="feedback">
                  
                    <h3>Send Feedback</h3>
                    <ul>
                    <li><a href="feedback_submit.php">Send Feedback</a></li>

            </div>


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
