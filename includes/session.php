<?php
session_start();
include('includes/database.php');

// Check if user is not logged in, but has a remember_token cookie
if (!isset($_SESSION['logged_in']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    $hashedToken = hash('sha256', $token);

    // Prepare SQL to find user with this token
    $stmt = $conn->prepare("SELECT username, role_id, user_id, token_expiry FROM users WHERE remember_token = ?");
    $stmt->bind_param("s", $hashedToken);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Optional: Check if token has expired
        if (isset($user['token_expiry']) && strtotime($user['token_expiry']) < time()) {
            // Expired token: remove cookie
            setcookie('remember_token', '', time() - 3600, "/");
            header("Location: login.php");
            exit();
        }

        // Log the user in
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role_id'] = $user['role_id'];

        session_regenerate_id(true); // Prevent session fixation
    } else {
        // Invalid token, delete cookie
        setcookie('remember_token', '', time() - 3600, "/");
    }
}

// Redirect if still not logged in
if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit();
}
?>
