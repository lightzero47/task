<?php
include('includes/session.php');
include('includes/header.php');
include('includes/navbar.php');
include('includes/database.php');

$username = isset($_GET['username']) ? $_GET['username'] : '';
$user = [
    'user_id' => '',
    'name' => '',
    'email' => '',
    'username' => '',
    'role_id' => ''
];
$errors = [];
$success = '';

// Fetch existing user data
if ($username !== '') {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        $errors[] = "User not found.";
    }
    $stmt->close();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    
    $role_id = intval($_POST["role_id"]);
    $user_id = intval($_POST["user_id"]);
    $password = $_POST["password"];

    // Validate input
    if (empty($name)) $errors[] = "Name is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if (empty($username)) $errors[] = "Username is required.";
    if ($role_id <= 0) $errors[] = "Role is required.";

    // Update if no errors
    if (empty($errors)) {
        if (!empty($password)) {
            // Password change included
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET name=?, password_hash=?, role_id=? WHERE user_id=?");
            $stmt->bind_param("ssii", $name, $password_hash, $role_id, $user_id);
        } else {
            // No password change
            $stmt = $conn->prepare("UPDATE users SET name=?, role_id=? WHERE user_id=?");
            $stmt->bind_param("sii", $name, $role_id, $user_id);
        }

        if ($stmt->execute()) {
            $success = "User updated successfully.";
            $user = [
                'user_id' => $user_id,
                'name' => $name,
                'email' => $email,
                'username' => $username,
                'role_id' => $role_id
            ];
        } else {
            $errors[] = "Update failed: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Get roles based on the current user's role
$roles = [];
$current_user_role_id = $user['role_id'];


if ($current_user_role_id === 1) {
    // Admin: show all roles
    $stmt = $conn->prepare("SELECT role_id, role_name FROM roles");
} else {
    // Non-admin: exclude admin role (role_id != 1)
    $stmt = $conn->prepare("SELECT role_id, role_name FROM roles WHERE role_id != 1");
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $roles[] = $row;
}

$stmt->close();
$conn->close();
?>
<div style="display: flex; justify-content: center; align-items: center; margin-top:20px">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <strong>Update User</strong>
            </div>
            <div class="card-body card-block">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $err) echo "<p>$err</p>"; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <form action="" method="post" class="form-horizontal">
                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>">
                    <input type="hidden" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">

                    <div class="form-group">
                        <label for="name" class="form-control-label">Full Name</label>
                        <input type="text" id="name" name="name" class="form-control"
                            value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="username" class="form-control-label">Username</label>
                        <input type="text" id="username" name="username" class="form-control"
                            value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-control-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control"
                            value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-control-label">Password (leave blank to keep current)</label>
                        <input type="password" id="password" name="password" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="role_id" class="form-control-label">Role</label>
                        <select name="role_id" id="role_id" class="form-control" required>
                            <option value="">-- Select Role --</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role['role_id']; ?>" <?php echo $role['role_id'] == $user['role_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($role['role_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa fa-dot-circle-o"></i> Update
                    </button>
                    <a href="user_list.php" class="btn btn-secondary btn-sm">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include('includes/footer.php'); ?>
