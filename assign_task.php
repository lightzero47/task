<?php
include('includes/session.php');
include('includes/header.php');
include('includes/navbar.php');

// Fetch tasks
$tasks = [];
$taskResult = $conn->query("SELECT task_id, title FROM tasks WHERE is_active = 1");
while ($row = $taskResult->fetch_assoc()) {
    $tasks[] = $row;
}

// Fetch users
$users = [];
$userResult = $conn->query("SELECT user_id, name FROM users WHERE is_active = 1");
while ($row = $userResult->fetch_assoc()) {
    $users[] = $row;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = $_POST['task_id'];
    $user_id = $_POST['user_id'];

    $stmt = $conn->prepare("INSERT INTO task_assignments (task_id, user_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $task_id, $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('Task assigned successfully'); window.location='assign_task.php';</script>";
        exit();
    } else {
        echo "Error assigning task: " . $stmt->error;
    }
}
?>

<div class="container mt-4">
    <h2>Assign Task to User</h2>
    <form method="POST">
        <div class="form-group">
            <label for="task_id">Select Task:</label>
            <select name="task_id" id="task_id" class="form-control" required>
                <option value="">-- Select Task --</option>
                <?php foreach ($tasks as $task): ?>
                    <option value="<?= $task['task_id'] ?>"><?= htmlspecialchars($task['title']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="user_id">Select User:</label>
            <select name="user_id" id="user_id" class="form-control" required>
                <option value="">-- Select User --</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['user_id'] ?>"><?= htmlspecialchars($user['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Assign Task</button>
        <a href="assign_task.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include('includes/footer.php'); ?>
