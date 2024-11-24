<?php
session_start();
$alertMessage = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    if ($to) {
        $subject = 'RESET PASSWORD LINK';
        $message = 'Click on the link below to reset your password: http://localhost/risto/rist/reset_password.php'; ;
        $headers = 'From: Admin';

        if (mail($to, $subject, $message, $headers )) {
            $_SESSION['email'] = $to;
            $alertMessage = "Swal.fire('Success', 'Email sent successfully.', 'success');";
        } else {
            $alertMessage = "Swal.fire('Error', 'Failed to send email.', 'error');";
        }
    } else {
        $alertMessage = "Swal.fire('Error', 'Invalid email address.', 'error');";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<style>
    .form {
        width: 50rem;
    }
</style>

<body>

    <div class="forgot-page container-fluid vh-100 d-flex justify-content-center align-items-center">
        <div class="form shadow p-5">
            <form action="forget_password_view.php" method="post">
                <div class="form-group">
                    <div class="h1">Provide your email</div> 
                    <input class="form-control mt-2" type="email" name="email" id="email" placeholder="Enter valid email" required>
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