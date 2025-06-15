<?php
include('includes/session.php'); 
include('includes/header.php'); 
include('includes/navbar.php'); 

$isAdmin = ($_SESSION['role_id'] ?? 0) == 1;

$task_id = $_GET['task_id'] ?? null;
$task = [
    'title' => '',
    'description' => '',
    'deadline' => '',
    'status' => 'not_started',
    'admin_status' => '',
    'created_by' => '',
    'is_active' => 1
];

// Load existing task data if editing
if ($task_id) {
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE task_id = ?");
    $stmt->bind_param("i", $task_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $task = $result->fetch_assoc();
}

// Load user list for assigning task
$users = [];
$res = $conn->query("SELECT user_id, name FROM users WHERE is_active = 1");
while ($row = $res->fetch_assoc()) {
    $users[] = $row;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $deadline = $_POST['deadline'];
    $status = $_POST['status'];
    $admin_status = $isAdmin ? ($_POST['admin_status'] ?? null) : null;
    $created_by = $_POST['created_by'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if ($task_id) {
        if ($isAdmin) {
            $stmt = $conn->prepare("UPDATE tasks SET title=?, description=?, deadline=?, status=?, admin_status=?, created_by=?, is_active=? WHERE task_id=?");
            $stmt->bind_param("ssssssii", $title, $description, $deadline, $status, $admin_status, $created_by, $is_active, $task_id);
        } else {
            $stmt = $conn->prepare("UPDATE tasks SET title=?, description=?, deadline=?, status=?, created_by=?, is_active=? WHERE task_id=?");
            $stmt->bind_param("ssssiii", $title, $description, $deadline, $status, $created_by, $is_active, $task_id);
        }
    } else {
        if ($isAdmin) {
            $stmt = $conn->prepare("INSERT INTO tasks (title, description, deadline, status, admin_status, created_by, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssi", $title, $description, $deadline, $status, $admin_status, $created_by, $is_active);
        } else {
            $stmt = $conn->prepare("INSERT INTO tasks (title, description, deadline, status, created_by, is_active) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssii", $title, $description, $deadline, $status, $created_by, $is_active);
        }
    }

    if ($stmt->execute()) {
        echo "<script>alert('Task saved successfully'); window.location='all_tasks.php';</script>";
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!-- Task Form -->
<div class="container">
    <h2><?= $task_id ? 'Edit' : 'Create'; ?> Task</h2>
    <form method="POST">
        <div class="form-group">
            <label>Title:</label>
            <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($task['title']) ?>">
        </div>

        <div class="form-group">
            <label>Description:</label>
            <textarea name="description" class="form-control"><?= htmlspecialchars($task['description']) ?></textarea>
        </div>

        <div class="form-group">
            <label>Deadline:</label>
            <input type="datetime-local" name="deadline" class="form-control" value="<?= $task['deadline'] ? date('Y-m-d\TH:i', strtotime($task['deadline'])) : '' ?>">
        </div>

        <div class="form-group">
            <label>Status:</label>
            <select name="status" class="form-control" required>
                <?php
                $statuses = ['not_started', 'ongoing', 'completed'];
                foreach ($statuses as $status_option) {
                    $selected = ($task['status'] == $status_option) ? 'selected' : '';
                    echo "<option value=\"$status_option\" $selected>" . ucfirst(str_replace('_', ' ', $status_option)) . "</option>";
                }
                ?>
            </select>
        </div>

        <?php if ($isAdmin): ?>
            <div class="form-group">
                <label>Admin Status:</label>
                <select name="admin_status" class="form-control">
                    <option value="">-- Select Admin Status --</option>
                    <?php
                    $adminStatuses = ['verified', 'not_accepted'];
                    foreach ($adminStatuses as $admin_status_option) {
                        $selected = ($task['admin_status'] == $admin_status_option) ? 'selected' : '';
                        echo "<option value=\"$admin_status_option\" $selected>" . ucfirst(str_replace('_', ' ', $admin_status_option)) . "</option>";
                    }
                    ?>
                </select>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label>Created by:</label>
            <select name="created_by" class="form-control" required>
                <option value="">-- Select User --</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['user_id'] ?>" <?= ($task['created_by'] == $user['user_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($user['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group form-check">
            <input type="checkbox" name="is_active" class="form-check-input" id="is_active" <?= ($task['is_active']) ? 'checked' : '' ?>>
            <label class="form-check-label" for="is_active">Active</label>
        </div>

        <button type="submit" class="btn btn-success"><?= $task_id ? 'Update' : 'Create'; ?> Task</button>
        <a href="all_tasks.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include('includes/footer.php'); ?>
