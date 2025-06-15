<?php
include('includes/session.php');
include('includes/header.php');
include('includes/navbar.php');

// Handle delete if requested
if (isset($_GET['delete_id'])) {
    $assignment_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM task_assignments WHERE assignment_id = ?");
    $stmt->bind_param("i", $assignment_id);
    if ($stmt->execute()) {
        echo "<script>alert('Assignment deleted'); window.location='task_assignments.php';</script>";
        exit();
    } else {
        echo "Error deleting assignment: " . $stmt->error;
    }
}

// Fetch assignments
$query = "
    SELECT ta.assignment_id, t.title AS task_title, u.name AS user_name, ta.assigned_at
    FROM task_assignments ta
    JOIN tasks t ON ta.task_id = t.task_id
    JOIN users u ON ta.user_id = u.user_id
    ORDER BY ta.assigned_at DESC
";
$result = $conn->query($query);
?>

<div class="container mt-4">
    <h2>Task Assignments</h2>
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>Assignment ID</th>
                <th>Task Title</th>
                <th>User</th>
                <th>Assigned At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['assignment_id'] ?></td>
                        <td><?= htmlspecialchars($row['task_title']) ?></td>
                        <td><?= htmlspecialchars($row['user_name']) ?></td>
                        <td><?= $row['assigned_at'] ?></td>
                        <td>
                            <a href="?delete_id=<?= $row['assignment_id'] ?>" onclick="return confirm('Delete this assignment?')" class="btn btn-sm btn-danger">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center">No task assignments found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include('includes/footer.php'); ?>
