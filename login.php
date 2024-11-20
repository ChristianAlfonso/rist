<?php
session_start();
include('db.php'); // Include your database connection

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Determine the correct table based on role
    $query = "";
    if ($role === 'admin') {
        $query = "SELECT * FROM admin WHERE username='$username'";
    } elseif ($role === 'teacher') {
        $query = "SELECT * FROM teachers WHERE username='$username'";
    } elseif ($role === 'parent') {
        $query = "SELECT * FROM parents WHERE username='$username'";
    } elseif ($role === 'student') {
        $query = "SELECT * FROM students WHERE username='$username'";
    }

    // Execute the query
    if ($query) {
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password'])) {
                // Set session variables based on role
                $_SESSION[$role . '_logged_in'] = true;
                $_SESSION[$role . '_username'] = $username;

                // Redirect to the appropriate dashboard
                if ($role === 'admin') {
                    header("Location: admin_dashboard.php");
                } elseif ($role === 'teacher') {
                    header("Location: teacher_dashboard.php");
                } elseif ($role === 'parent') {
                    header("Location: parent_dashboard.php");
                } elseif ($role === 'student') {
                    header("Location: student_dashboard.php");
                }
                exit();
            } else {
                echo "<script>alert('Incorrect password.');</script>";
            }
        } else {
            echo "<script>alert('User not found.');</script>";
        }
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
    <title>Login</title>
    <script>
        function togglePasswordVisibility() {
            const passwordField = document.getElementById('password');
            const toggleButton = document.getElementById('togglePassword');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleButton.innerText = 'Hide Password';
            } else {
                passwordField.type = 'password';
                toggleButton.innerText = 'Show Password';
            }
        }
    </script>
</head>

<body>

    <div class="login-page vh-100 d-flex justify-content-center align-items-center">
        <div class="form p-5 shadow">
            <div class="form-title h1">
                Login
            </div>

            
            <form action="#">

                <div class="form-group  mt-4">
                    <label for="role" class="h5">Role:</label>
                    <select name="role" class="form-control" required>
                        <option value="" selected disabled>Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="teacher">Teacher</option>
                        <option value="parent">Parent</option>
                        <option value="student">Student</option>
                    </select>
                </div>

                <div class="form-group mt-3">
                    <label for="username">Username:</label>
                    <input type="text" name="username" class="form-control" required>
                </div>

                

                
                
                
            </form>
        </div>
    </div>

<!--
    <h2>Login</h2>
    <form method="POST" action="login.php">
        <label for="role">Role:</label><br>
        <select name="role" required>
                <option value="admin">Admin</option>
                <option value="teacher">Teacher</option>
            <option value="parent">Parent</option>
            <option value="student">Student</option>
        </select><br>

        <label for="username">Username:</label><br>
        <input type="text" name="username" required><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br>
        <button type="button" id="togglePassword" onclick="togglePasswordVisibility()">Show Password</button><br><br>

        <button type="submit">Login</button>
    </form>
    -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
