<?php
session_start();
include "config.php"; // Include database connection

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Get username from session
$username = isset($_SESSION["username"]) ? $_SESSION["username"] : "Guest";

// Fetch user details from the database
$user_id = $_SESSION["user_id"];
$query = "SELECT username, email, created_at, profile_picture FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$user) {
    echo "User not found.";
    exit();
}

// Set profile picture (default if not available)
$profile_picture = !empty($user["profile_picture"]) ? "uploads/" . htmlspecialchars($user["profile_picture"]) : "uploads/default.jpg";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card p-4 shadow">
            <div class="text-center">
                <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>

                <!-- Display Profile Picture -->
                <img src="<?php echo $profile_picture; ?>" class="rounded-circle img-thumbnail" width="150" height="150" alt="Profile Picture">
            </div>

            <hr>

            <!-- Profile Picture Upload Form -->
            <form action="upload_profile_picture.php" method="POST" enctype="multipart/form-data" class="mt-3">
                <input type="file" name="profile_picture" class="form-control mb-2" accept="image/*">
                <button type="submit" class="btn btn-primary">Upload Profile Picture</button>
            </form>

            <h4>Account Details</h4>
            <ul class="list-group">
                <li class="list-group-item"><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></li>
                <li class="list-group-item"><strong>Email:</strong> <?php echo htmlspecialchars($user["email"]); ?></li>
                <li class="list-group-item"><strong>Member Since:</strong> <?php echo htmlspecialchars($user["created_at"]); ?></li>
            </ul>

            <div class="mt-3 text-center">
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </div>
</body>
</html>
