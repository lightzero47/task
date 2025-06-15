<?php
include('includes/session.php');
include('includes/header.php');
include('includes/navbar.php');

// Default data
$data = [
    'task_id' => '',
    'user_id' => '',
    'text_content' => '',
    'media_url' => ''
];

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
    $task_id = isset($_POST['task_id']) ? (int)$_POST['task_id'] : 0;
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $text_content = trim($_POST['text_content']);
    $media_url = null;

    // Handle file upload
    if (!empty($_FILES['media']['name'])) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $originalName = $_FILES['media']['name'];
        $fileTmpPath = $_FILES['media']['tmp_name'];
        $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
        $fileSize = $_FILES['media']['size'];
        $fileError = $_FILES['media']['error'];

        // Limit file size to 2gb
        if ($fileSize > 2 * 1024 * 1024 * 1024) { // 2 GB in bytes
            echo "<div class='alert alert-warning'>File size exceeds 2GB.</div>";
            exit;
        }

        if ($fileError === UPLOAD_ERR_OK) {
            // Create unique file name
            $uniqueFileName = uniqid('file_', true) . '.' . $fileExtension;
            $targetPath = $uploadDir . $uniqueFileName;

            if (move_uploaded_file($fileTmpPath, $targetPath)) {
                $media_url = $targetPath;
            } else {
                echo "<div class='alert alert-danger'>File upload failed.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>File upload error. Code: $fileError</div>";
        }
    }

    // Insert submission into database
    $stmt = $conn->prepare("INSERT INTO task_submissions (task_id, user_id, text_content, media_url) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $task_id, $user_id, $text_content, $media_url);

    if ($stmt->execute()) {
        echo "<script>alert('Task submitted successfully'); window.location='task_submissions.php';</script>";
        exit();
    } else {
        echo "<div class='alert alert-danger'>Database error: " . htmlspecialchars($stmt->error) . "</div>";
    }
}
?>

<div class="container mt-4">
    <h2 class="text-center">Submit Task</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Task:</label>
            <select name="task_id" class="form-control" required>
                <option value="">-- Select Task --</option>
                <?php foreach ($tasks as $task): ?>
                    <option value="<?= $task['task_id'] ?>"><?= htmlspecialchars($task['title']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>User:</label>
            <select name="user_id" class="form-control" required>
                <option value="">-- Select User --</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['user_id'] ?>"><?= htmlspecialchars($user['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Text Content:</label>
            <textarea name="text_content" class="form-control" rows="5" required><?= htmlspecialchars($data['text_content']) ?></textarea>
        </div>

        <div class="form-group">
            <label>Upload File (Any type):</label>
            <input type="file" name="media" class="form-control-file">
            <small class="text-muted">Max size: 2GB. Any file type is accepted.</small>
        </div>

        <button type="submit" class="btn btn-success">Submit</button>
        <a href="task_submissions.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include('includes/footer.php'); ?>
