<?php
include('includes/session.php');
include('includes/database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = intval($_POST['task_id']);
    $status = $_POST['status'] ?? '';
    $admin_status = $_POST['admin_status'] ?? null;

    $isAdmin = ($_SESSION['role_id'] ?? 0) == 1;

    // Validate status values
    $valid_statuses = ['not_started', 'ongoing', 'completed'];
    $valid_admin_statuses = ['verified', 'not_accepted', ''];

    if (!in_array($status, $valid_statuses)) {
        die("Invalid general status value.");
    }

    if ($isAdmin && !in_array($admin_status, $valid_admin_statuses)) {
        die("Invalid admin status value.");
    }

    if ($isAdmin) {
        // Admin can update both status and admin_status
        $stmt = $conn->prepare("UPDATE tasks SET status = ?, admin_status = ? WHERE task_id = ?");
        $stmt->bind_param("ssi", $status, $admin_status, $task_id);
    } else {
        // Regular user: update only general status
        $stmt = $conn->prepare("UPDATE tasks SET status = ? WHERE task_id = ?");
        $stmt->bind_param("si", $status, $task_id);
    }

    if ($stmt->execute()) {
        header("Location: all_tasks.php");
        exit;
    } else {
        echo "Error updating task: " . $conn->error;
    }

    $stmt->close();
}
?>
