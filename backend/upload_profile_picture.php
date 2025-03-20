<?php
session_start();
include "config.php"; // Include database connection

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Check if file was uploaded
if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] == 0) {
    $target_dir = "uploads/";
    $file_name = basename($_FILES["profile_picture"]["name"]);
    $target_file = $target_dir . $file_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed_extensions = ["jpg", "jpeg", "png", "gif"];

    // Validate file type
    if (!in_array($imageFileType, $allowed_extensions)) {
        echo "Only JPG, JPEG, PNG, and GIF files are allowed.";
        exit();
    }

    // Move the uploaded file to the uploads directory
    if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
        // Update profile picture path in the database
        $query = "UPDATE users SET profile_picture = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $file_name, $user_id);
        $stmt->execute();
        $stmt->close();
        $conn->close();

        // Redirect back to dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error uploading file.";
    }
} else {
    echo "No file uploaded or an error occurred.";
}
?>
