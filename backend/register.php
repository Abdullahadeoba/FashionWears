<?php
session_start();
include "config.php"; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    // Check if all fields are filled
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION["error"] = "All fields are required!";
        header("Location: register.html");
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION["error"] = "Invalid email format!";
        header("Location: register.html");
        exit();
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        $_SESSION["error"] = "Passwords do not match!";
        header("Location: register.html");
        exit();
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Default profile picture path
    $default_profile_picture = "uploads/default.jpg";

    // Check if username or email already exists
    $check_query = "SELECT id FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION["error"] = "Username or Email already exists!";
        header("Location: register.html");
        exit();
    }
    $stmt->close();

    // Insert new user into database
    $query = "INSERT INTO users (username, email, password, profile_picture, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $username, $email, $hashed_password, $default_profile_picture);

    if ($stmt->execute()) {
        // Store user details in session
        $_SESSION["user_id"] = $stmt->insert_id;
        $_SESSION["username"] = $username;
        $_SESSION["email"] = $email;

        // Redirect to dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION["error"] = "Registration failed. Try again!";
        header("Location: register.html");
        exit();
    }

    // Close connection
    $stmt->close();
    $conn->close();
}
?>
