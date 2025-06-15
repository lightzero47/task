<?php
include('includes/session.php'); 
include('includes/header.php'); 
include('includes/navbar.php'); 
include('includes/form.php'); 

$id = $_GET['id'] ?? null;
$data = ['role_name' => ''];

// Load existing role data if editing
if ($id) {
    $stmt = $conn->prepare("SELECT * FROM roles WHERE role_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'] ?? null;
    $role_name = trim($_POST['role_name']);

    if (empty($role_name)) {
        echo "<script>alert('Role name is required');</script>";
    } else {
        if ($id) {
            // Update role
            $stmt = $conn->prepare("UPDATE roles SET role_name = ? WHERE role_id = ?");
            $stmt->bind_param("si", $role_name, $id);
        } else {
            // Insert new role
            $stmt = $conn->prepare("INSERT INTO roles (role_name) VALUES (?)");
            $stmt->bind_param("s", $role_name);
        }

        if ($stmt->execute()) {
            echo "<script>alert('Role saved successfully'); window.location='roles_table.php';</script>";
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}
?>

<!-- Role Form -->
<div class="container-fluid">
    <h2><?= $id ? 'Edit' : 'Add'; ?> Role</h2>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $data['role_id'] ?? ''; ?>">

        <div class="form-group">
            <label for="role_name">Role Name:</label>
            <input type="text" name="role_name" class="form-control" id="role_name" value="<?= htmlspecialchars($data['role_name']); ?>" required>
        </div>

        <button type="submit" class="btn btn-success"><?= $id ? 'Update' : 'Create'; ?> Role</button>
        <a href="roles_table.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include('includes/footer.php'); ?>
