<?php
include('includes/session.php');
include('includes/database.php');

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Optional: Check for users assigned to this role before deleting
    $checkStmt = $conn->prepare("SELECT COUNT(*) AS user_count FROM users WHERE role_id = ?");
    $checkStmt->bind_param("i", $id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $userCount = $checkResult->fetch_assoc()['user_count'];

    if ($userCount > 0) {
        echo "<script>alert('Cannot delete this role because it is assigned to one or more users.');</script>";
    } else {
        // Proceed to delete role
        $stmt = $conn->prepare("DELETE FROM roles WHERE role_id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "<script>alert('Role deleted successfully');</script>";
        } else {
            echo "<script>alert('Error deleting role');</script>";
        }
    }
}

echo "<script>window.location.href='roles_table.php';</script>";
?>
