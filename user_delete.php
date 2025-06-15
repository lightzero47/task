<?php
include('includes/session.php');
include('includes/database.php');

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Deleted successfully');</script>";
    } else {
        echo "<script>alert('Error deleting ');</script>";
    }
}

echo "<script>window.location.href='user_list.php';</script>";
?>
