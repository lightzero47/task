<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['logged_in'])) {
    header("Location: dashboard.php");
    exit();
}

include('includes/database.php');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember_me']);

    if (empty($username) || strlen($username) < 5 || strlen($username) > 40) {
        die("Invalid username length.");
    }

    $username = filter_var($username, FILTER_SANITIZE_STRING);

    if (isset($_SESSION['lockout_time']) && $_SESSION['lockout_time'] > time()) {
        die("Locked out. Try again later.");
    }

    $stmt = $conn->prepare("SELECT u.username, u.email, u.password_hash, u.is_active, u.remember_token, r.role_name, r.role_id
                            FROM users u
                            JOIN roles r ON u.role_id = r.role_id
                            WHERE u.username = ? OR u.email = ?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password_hash']) && $row['is_active'] == 1) {
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role_name'];
             $_SESSION['role_id'] = $row['role_id'];
            $_SESSION['login_attempts'] = 0;

            // Handle Remember Me
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $hashedToken = hash('sha256', $token);

                $update = $conn->prepare("UPDATE users SET remember_token = ? WHERE username = ?");
                $update->bind_param("ss", $hashedToken, $row['username']);
                $update->execute();

                setcookie("remember_token", $token, time() + (30 * 24 * 60 * 60), "/", "", true, true);
            }

            header("Location: dashboard.php");
            exit();
        }
    }

    handleLoginFailure();
}

// Login failure handling
function handleLoginFailure() {
    if (isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts']++;
    } else {
        $_SESSION['login_attempts'] = 1;
    }

    if ($_SESSION['login_attempts'] >= 5) {
        $_SESSION['lockout_time'] = time() + (5 * 60); // 5 minutes lockout
        header("Location: login.php?error=lockout");
    } else {
        echo "<script>
                alert('Invalid credentials or account not approved.');
                window.location.href = 'login.php?error=invalid_credentials';
            </script>";

    }
    exit();
}
?>


<!-- HTML Section -->
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Login | Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-dark">

<div class="sufee-login d-flex align-content-center flex-wrap">
    <div class="container">
        <div class="login-content">
            <div class="login-form">
                <form method="POST" action="#">
                    <div class="form-group">
                        <label>Username or Email</label>
                        <input type="text" name="username" class="form-control" placeholder="Enter your username or email">
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Password">
                    </div>
                    <div class="checkbox d-flex justify-content-between">
                        <label><input type="checkbox"> Remember Me</label>
                        <a href="password.php">Forgot Password?</a>
                    </div>
                    <button type="submit" class="btn btn-success btn-flat m-b-30 m-t-30">Sign in</button>
                    <div class="register-link m-t-15 text-center">
                        <p>Don't have an account? <a href="register.php">Sign Up Here</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
