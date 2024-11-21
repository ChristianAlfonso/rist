<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    if ($to) {
        $subject = 'RESET PASSWORD LINK';
        $message = 'Click on the link below to reset your password: http://localhost/rist/reset_password.php'; ;
        $headers = 'From: Admin';

        if (mail($to, $subject, $message, $headers )) {
            echo "Email sent successfully.";
            $_SESSION['email'] = $to;
        } else {
            echo "Failed to send email.";
        }
    } else {
        echo "Invalid email address.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password</title>
</head>
<body>
    <form action="forget_password_view.php" method="post">
        <label for="email">Please provide your email </label>
        <input type="email" name="email" id="email" required>
        <button type="submit">Submit</button>
    </form>
</body>
<script>
  
</script>
</html>