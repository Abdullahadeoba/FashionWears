<?php
session_start();
include "config.php"; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = trim($_POST["login"]); // Can be username or email
    $password = trim($_POST["password"]);

    if (empty($login) || empty($password)) {
        echo "Both fields are required!";
        exit();
    }

    // Check if login is username or email
    $query = "SELECT id, username, email, password FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $login, $login);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {
        // Set session variables
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["email"] = $user["email"];

        // Redirect to dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Invalid username/email or password!";
    }

    $stmt->close();
    $conn->close();
}
?>
