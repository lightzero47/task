<?php

include('includes/database.php');

include('includes/session.php');



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Check if the username is already taken
    if ($action === 'check-username') {
        $username = $conn->real_escape_string($_POST['username']);
        $sql = "SELECT COUNT(*) FROM users WHERE username = '$username'";
        $result = $conn->query($sql);
        $row = $result->fetch_row();
        $exists = $row[0] > 0;
        echo json_encode(['exists' => $exists]);
        exit;
    }

    // Check if the email is already taken
    if ($action === 'check-email') {
        $email = $conn->real_escape_string($_POST['email']);
        $sql = "SELECT COUNT(*) FROM users WHERE email = '$email'";
        $result = $conn->query($sql);
        $row = $result->fetch_row();
        $exists = $row[0] > 0;
        echo json_encode(['exists' => $exists]);
        exit;
    }

    // User registration
    if ($action === 'register') {
        $name = $conn->real_escape_string($_POST['name']);
        $email = $conn->real_escape_string($_POST['email']);
        $username = $conn->real_escape_string($_POST['username']);
        $password = $_POST['password'];
        $role_id = (int) $_POST['role_id']; // Get role_id from dropdown

        // Hash password before saving it
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        // Check if username or email already exists
        $sql = "SELECT COUNT(*) FROM users WHERE username = '$username' OR email = '$email'";
        $result = $conn->query($sql);
        $row = $result->fetch_row();
        if ($row[0] > 0) {
            echo json_encode(['success' => false, 'error' => 'Username or email already exists']);
            exit;
        }

        // Insert new user into the database (is_active is set to false)
        $sql = "INSERT INTO users (name, email, username, password_hash, role_id, is_active) 
                VALUES ('$name', '$email', '$username', '$passwordHash', $role_id, false)";
        if ($conn->query($sql) === TRUE) {
             echo json_encode(['success' => true, 'message' => 'User successfully created, redirecting to login page...']);
             exit;
        } else {
            echo json_encode(['success' => false, 'error' => 'Error: ' . $conn->error]);
        }
    }
}

// Fetch roles for the dropdown
$sql = "SELECT * FROM roles";
$roles_result = $conn->query($sql);
$roles = [];
while ($row = $roles_result->fetch_assoc()) {
    $roles[] = $row;
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel - Register</title>
    <meta name="description" content="Admin Panel">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/normalize.css@8.0.0/normalize.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/cs-skin-elastic.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-dark">

    <div class="sufee-login d-flex align-content-center flex-wrap">
        <div class="container">
            <div class="login-content">
                <div class="login-form">
                    <form id="registerForm">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" placeholder="Full Name" required>
                        </div>
                        <div class="form-group">
                            <label>User Name</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="User Name" required>
                            <small id="username-error" class="form-text text-danger"></small>
                        </div>
                        <div class="form-group">
                            <label>Email address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                            <small id="email-error" class="form-text text-danger"></small>
                        </div>
                        
                        <div class="form-group">
                            <label>Role</label>
                            <select class="form-control" name="role_id" required>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['role_id']; ?>"><?= $role['role_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Password" required>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" required> Agree the terms and policy
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-flat m-b-30 m-t-30">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            // Real-time validation for username
            $('#username').on('input', function() {
                let username = $(this).val();
                if (username) {
                    $.ajax({
                        url: '',  // Current page
                        type: 'POST',
                        data: { username: username, action: 'check-username' },
                        success: function(response) {
                            let data = JSON.parse(response);
                            if (data.exists) {
                                $('#username-error').text('Username is already taken.');
                            } else {
                                $('#username-error').text('');
                            }
                        }
                    });
                }
            });

            // Real-time validation for email
            $('#email').on('input', function() {
                let email = $(this).val();
                if (email) {
                    $.ajax({
                        url: '',  // Current page
                        type: 'POST',
                        data: { email: email, action: 'check-email' },
                        success: function(response) {
                            let data = JSON.parse(response);
                            if (data.exists) {
                                $('#email-error').text('Email is already registered.');
                            } else {
                                $('#email-error').text('');
                            }
                        }
                    });
                }
            });

            // Handle form submission
            $('#registerForm').submit(function(e) {
                e.preventDefault();
                let formData = $(this).serialize();

                $.ajax({
                    url: '',  // Current page
                    type: 'POST',
                    data: formData + '&action=register',  // Add action to identify register request
                    success: function(response) {
                        let data = JSON.parse(response);
                        if (data.success) {
                            alert(data.message);  // Show success message
                            setTimeout(function() {
                                window.location.href = 'login.php';  // Redirect to login page
                            }, 20); // Delay for 0.02 seconds before redirect
                        } else {
                            alert('Error during registration: ' + data.error);
                        }
                    }
                });
            });
        });
    </script>

</body>
</html>
