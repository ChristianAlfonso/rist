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
    <link rel="stylesheet" href="styles.css">

    <title>Admin Announcements</title>
</head>
<body>

    <h2><?php echo isset($edit_announcement) ? 'Edit Announcement' : 'Post Announcement'; ?></h2>

    <!-- Announcement Form for adding/editing -->
    <form method="POST" action="admin_announcements.php">
        <input type="hidden" name="id" value="<?php echo isset($edit_announcement) ? $edit_announcement['id'] : ''; ?>">
        
        <label for="title">Title:</label><br>
        <input type="text" name="title" value="<?php echo isset($edit_announcement) ? $edit_announcement['title'] : ''; ?>" required><br>

        <label for="content">Content:</label><br>
        <textarea name="content" rows="5" required><?php echo isset($edit_announcement) ? $edit_announcement['content'] : ''; ?></textarea><br>

        <label for="audience">Audience:</label><br>
        <select name="audience" required>
            <option value="students" <?php if (isset($edit_announcement) && $edit_announcement['audience'] == 'students') echo 'selected'; ?>>Students</option>
            <option value="parents" <?php if (isset($edit_announcement) && $edit_announcement['audience'] == 'parents') echo 'selected'; ?>>Parents</option>
            <option value="teachers" <?php if (isset($edit_announcement) && $edit_announcement['audience'] == 'teachers') echo 'selected'; ?>>Teachers</option>
            <option value="all" <?php if (isset($edit_announcement) && $edit_announcement['audience'] == 'all') echo 'selected'; ?>>All</option>
        </select><br><br>

        <button type="submit" name="<?php echo isset($edit_announcement) ? 'update_announcement' : 'add_announcement'; ?>">
            <?php echo isset($edit_announcement) ? 'Update Announcement' : 'Post Announcement'; ?>
        </button>
    </form>

    <hr>

    <h2>Manage Announcements</h2>

    <!-- List of announcements with Edit and Delete options -->
    <?php
    // Fetch all announcements
    $query = "SELECT * FROM announcements ORDER BY date_posted DESC";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        echo "<table border='1'>";
        echo "<tr><th>Title</th><th>Content</th><th>Audience</th><th>Date Posted</th><th>Actions</th></tr>";

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['title'] . "</td>";
            echo "<td>" . $row['content'] . "</td>";
            echo "<td>" . $row['audience'] . "</td>";
            echo "<td>" . $row['date_posted'] . "</td>";
            echo "<td>
                    <a href='admin_announcements.php?edit_id=" . $row['id'] . "'>Edit</a> |
                    <a href='admin_announcements.php?delete_id=" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to delete this announcement?\");'>Delete</a>
                  </td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "No announcements found.";
    }
    ?>
<a href="admin_dashboard.php">Back</a>
</body>
</html>
