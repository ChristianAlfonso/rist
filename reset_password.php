<?php
session_start();
include('db.php'); // Include your database connection
$email = $_SESSION['email'];
$tables = ['admin', 'teachers', 'parents', 'students'];
for($i = 0; $i < count($tables); $i++) {
    $query = "SELECT * FROM $tables[$i] WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        break;
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $hash_password = password_hash($password, PASSWORD_DEFAULT);
    if ($password === $confirm_password) {
        $query = "UPDATE $tables[$i] SET password='$hash_password' WHERE email='$email'";
        if (mysqli_query($conn, $query)) {
            echo "Password reset successfully.";
            session_destroy();
            header("Location: login.php");
        } else {
            echo "Failed to reset password.";
        }
    } else {
        echo "Passwords do not match.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset password</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <div class="forgot-page container-fluid vh-100 d-flex justify-content-center align-items-center">
        <div class="form shadow p-5">
            <h1>Change password</h1>
            <form action="reset_password.php" method="post">

                <div class="form-group">
                    <label for="password">New password</label>
                    <input class="form-control" type="password" name="password" id="password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm password</label>
                    <input class="form-control" type="password" name="confirm_password" id="confirm_password" required>
                </div>

                
                <div class="form-group mt-3 d-flex justify-content-end" style="gap: 5px; flex-wrap: wrap;">
                    <a class="btn btn-dark" href="login.php">Back</a>
                    <button class="btn btn-danger" type="submit">Submit</button>
                </div>

            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script>
        <?php if ($alertMessage) { echo $alertMessage; } ?>
    </script>
</body>
</html>