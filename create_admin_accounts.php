<?php
include 'db.php';

function createAdminAccount($username, $email, $password) {
    global $conn;
   
    // Prepare the SQL statement with username, email, and password
    $stmt = $conn->prepare("INSERT INTO admin (username, email, password) VALUES (?, ?, ?)");
    
    if ($stmt) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT); // Hash the password
        $stmt->bind_param("sss", $username, $email, $hashedPassword); 
        
        if ($stmt->execute()) {
            echo "New admin account created successfully: $username ($email)\n";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close(); // Close the statement
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}

// Example: Create admin accounts
//createAdminAccount('admin2', 'admin2@example.com', 'earvhs@admin2');

// Close the connection
$conn->close(); // Close the connection
?>
