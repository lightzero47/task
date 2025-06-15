<?php
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['logged_in'])) {
    // Redirect to the login page
    header("Location: login.php");
    exit();
}
else{
    header("Location: dashboard.php");
    exit();
}
?>