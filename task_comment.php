<?php
include('includes/session.php');
include('includes/header.php');
include('includes/navbar.php');

// Fetch tasks
$tasks = [];
$result = $conn->query("SELECT task_id, title FROM tasks WHERE is_active = 1");
while ($row = $result->fetch_assoc()) {
    $tasks[] = $row;
}

// Fetch users
$users = [];
$result = $conn->query("SELECT user_id, name FROM users WHERE is_active = 1");
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $task_id = $_POST['task_id'];
    $user_id = $_POST['user_id'];
    $comment = trim($_POST['comment']);

    $stmt = $conn->prepare("INSERT INTO task_comments (task_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $task_id, $user_id, $comment);

    if ($stmt->execute()) {
        echo "<script>alert('Comment submitted successfully'); window.location='task_comments.php';</script>";
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<div class="container mt-4">
    <h2 class="text-center">Add Task Comment</h2>
    <form method="POST">
        <div class="form-group">
            <label for="task_id">Select Task:</label>
            <select name="task_id" class="form-control" required>
                <option value="">-- Select Task --</option>
                <?php foreach ($tasks as $task): ?>
                    <option value="<?= $task['task_id'] ?>"><?= htmlspecialchars($task['title']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="user_id">Select User:</label>
            <select name="user_id" class="form-control" required>
                <option value="">-- Select User --</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['user_id'] ?>"><?= htmlspecialchars($user['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="comment">Comment:</label>
            <textarea name="comment" class="form-control" rows="4" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Submit Comment</button>
        <a href="task_comments.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include('includes/footer.php'); ?>
