<?php
session_start();
include "config.php"; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_or_email = trim($_POST["username_or_email"]);
    $password = trim($_POST["password"]);

    if (empty($username_or_email) || empty($password)) {
        $_SESSION["error"] = "All fields are required!";
        header("Location: login.html");
        exit();
    }

    // Check if input is username or email
    $query = "SELECT id, username, email, password FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username_or_email, $username_or_email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user["password"])) {
            // Store user details in session
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["email"] = $user["email"];

            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION["error"] = "Invalid password!";
        }
    } else {
        $_SESSION["error"] = "No account found with that username/email!";
    }

    // Redirect back to login
    header("Location: login.html");
    exit();
}
?>
