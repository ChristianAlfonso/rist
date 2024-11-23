<?php
session_start();
include('db.php');

// Check if the teacher is logged in
if (!isset($_SESSION['teacher_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Fetch the logged-in teacher's information
$username = $_SESSION['teacher_username'];
$query = $conn->prepare("SELECT id, subject, first_name, last_name, email FROM teachers WHERE username = ?");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();
$teacher = $result->fetch_assoc();
$teacher_id = $teacher['id']; // Get the teacher's ID

// Fetch announcements
$announcement_query = $conn->prepare("SELECT * FROM announcements ORDER BY date_posted DESC");
$announcement_query->execute();
$announcements_result = $announcement_query->get_result();

// Fetch subjects and sections added by this teacher
$subjects_query = $conn->prepare("SELECT subject_name, year_level, section FROM subjects_sections WHERE teacher_id = ?");
$subjects_query->bind_param("s", $teacher_id);
$subjects_query->execute();
$subjects_result = $subjects_query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    
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
                                <a href="#announcement" data-bs-toggle="tab" class="nav-link active">Announcement</a>
                            </li>
                            <li class="nav-item">
                                <a href="#information" data-bs-toggle="tab" class="nav-link">Information</a>
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
                        <a href="#announcement" data-bs-toggle="tab" class="nav-link active">Announcement</a>
                    </li>
                    <li class="nav-item">
                        <a href="#information" data-bs-toggle="tab" class="nav-link">Information</a>
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

            <div class="tab-pane active container-fluid" id="announcement">
               <h1 class="mt-5">Welcome, <?php echo htmlspecialchars($teacher['first_name']); ?>!</h1>

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

            <div class="tab-pane fade container-fluid" id="information">

                <div class="container-fluid shadow mt-5 p-5">
                    <h2>Your Information</h2>
                        <p><strong>Teacher ID:</strong> <?php echo htmlspecialchars($teacher['id']); ?></p>
                        <p><strong>Subject:</strong> <?php echo htmlspecialchars($teacher['subject']); ?></p>
                        <p><strong>Full Name:</strong> <?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($teacher['email']); ?></p>

                    <div class="container-fluid d-flex justify-content-end">
                        <a class="btn btn-dark " href="change_password.php">Change Password</a>
                    </div>
                        
                </div>

                <div class="container-fluid shadow p-5 mt-3">
                     <!-- Added Subjects and Sections -->
                      <h2>Your Added Subjects and Sections</h2>
                            <?php if ($subjects_result->num_rows > 0): ?>
                                <ul>
                                    <?php while ($subject = $subjects_result->fetch_assoc()): ?>
                                        <li>
                                            <?php echo htmlspecialchars($subject['year_level']) . " " . htmlspecialchars($subject['section']) . ": " . htmlspecialchars($subject['subject_name']); ?> 
                                            <a class="btn btn-sm btn-danger" href="view_subject_details.php?year=<?php echo urlencode($subject['year_level']); ?>&section=<?php echo urlencode($subject['section']); ?>&subject=<?php echo urlencode($subject['subject_name']); ?>">View</a>
                                        </li>
                                    <?php endwhile; ?>
                                </ul>
                            <?php else: ?>
                                <p>No subjects added yet.</p>
                            <?php endif; ?>

                    <div class="container-fluid d-flex justify-content-end">
                                   <a class="btn btn-danger" href="add_subject_section.php">Add Subject/Section</a>
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
    <script>
        // Auto close offcanvas when screen size changes
        window.addEventListener('resize', function() {
            if (window.innerWidth > 700) {
                var offcanvasElement = document.getElementById('offcanvasSidebar');
                var offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasElement);
                if (offcanvasInstance) {
                    offcanvasInstance.hide();
                }
            }
        });
    </script>
</body>
</html>
