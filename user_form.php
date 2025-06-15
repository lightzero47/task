<?php
include('includes/session.php'); 
include('includes/header.php'); 
include('includes/navbar.php'); 
include('includes/form.php'); 

$id = $_GET['id'] ?? null;
$data = [
    'name' => '',
    'email' => '',
    'username' => '',
    'role_id' => '',
    'is_active' => 1
];

// Fetch roles for dropdown
$roles = [];
$roleResult = $conn->query("SELECT role_id, role_name FROM roles");
while ($role = $roleResult->fetch_assoc()) {
    $roles[] = $role;
}

// Load existing user data
if ($id) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
}

// Form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = $_POST['password'] ?? '';
    $role_id = $_POST['role_id'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if (!empty($password)) {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
    }

    if ($id) {
        if (!empty($password)) {
            $stmt = $conn->prepare("UPDATE users SET name=?, email=?, username=?, password_hash=?, role_id=?, is_active=? WHERE user_id=?");
            $stmt->bind_param("ssssiii", $name, $email, $username, $password_hash, $role_id, $is_active, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET name=?, email=?, username=?, role_id=?, is_active=? WHERE user_id=?");
            $stmt->bind_param("sssiii", $name, $email, $username, $role_id, $is_active, $id);
        }
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, username, password_hash, role_id, is_active) VALUES (?, ?, ?, ?, ?, ?)");
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bind_param("ssssii", $name, $email, $username, $password_hash, $role_id, $is_active);
    }

    if ($stmt->execute()) {
        echo "<script>alert('User saved successfully'); window.location='user_list.php';</script>";
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>



<!-- Form structure -->
<div class="container-fluid">
    <h2><?= $id ? 'Edit' : 'Add'; ?> User</h2>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $data['user_id'] ?? ''; ?>">

        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" class="form-control" id="name" value="<?= htmlspecialchars($data['name']); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" class="form-control" id="email" value="<?= htmlspecialchars($data['email']); ?>" required>
        </div>

        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" class="form-control" id="username" value="<?= htmlspecialchars($data['username']); ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Password: <small class="text-muted"><?= $id ? '(Leave blank to keep current password)' : ''; ?></small></label>
            <input type="password" name="password" class="form-control" id="password" <?= $id ? '' : 'required'; ?>>
        </div>

        <div class="form-group">
            <label for="role_id">Role:</label>
            <select name="role_id" class="form-control" id="role_id" required>
                <option value="">-- Select Role --</option>
                <?php foreach ($roles as $role): ?>
                    <option value="<?= $role['role_id']; ?>" <?= ($data['role_id'] == $role['role_id']) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($role['role_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group form-check">
            <input type="checkbox" name="is_active" class="form-check-input" id="is_active" <?= ($data['is_active']) ? 'checked' : ''; ?>>
            <label class="form-check-label" for="is_active">Active</label>
        </div>

        <button type="submit" class="btn btn-success"><?= $id ? 'Update' : 'Create'; ?> User</button>
        <a href="user_list.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // DataTables or any necessary JS initialization
    });
</script>

<?php include('includes/footer.php'); ?>
