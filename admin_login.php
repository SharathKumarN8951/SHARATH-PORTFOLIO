<?php
// admin_login.php
require "config.php";
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

/* If already logged in, go to dashboard */
if (!empty($_SESSION['admin_logged_in']) && !empty($_SESSION['admin_username'])) {
    header("Location: admin_dashboard.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === "" || $password === "") {
        $error = "Please enter username and password.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $user['username'];
                header("Location: admin_dashboard.php");
                exit;
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username or password.";
        }
        $stmt->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | Portfolio</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #020617;
        }
        .admin-login-box {
            width: 100%;
            max-width: 350px;
            background: #020617;
            border-radius: 16px;
            border: 1px solid #1f2937;
            padding: 20px;
        }
        .admin-login-box h2 {
            text-align: center;
            margin-bottom: 15px;
            color: #38bdf8;
        }
        .admin-login-box .error {
            background: #7f1d1d;
            color: #fee2e2;
            padding: 8px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 10px;
            text-align: center;
        }
        .admin-login-box .form-group {
            margin-bottom: 12px;
        }
        .admin-login-box label {
            font-size: 13px;
            margin-bottom: 4px;
            display: block;
        }
        .admin-login-box input {
            width: 100%;
            padding: 8px 10px;
            border-radius: 8px;
            border: 1px solid #4b5563;
            background: #020617;
            color: #e5e7eb;
            font-size: 13px;
        }
        .admin-login-box button {
            width: 100%;
            margin-top: 8px;
        }
        .back-home {
            margin-top: 10px;
            text-align: center;
            font-size: 13px;
        }
        .back-home a {
            color: #38bdf8;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="admin-login-page">
    <div class="admin-login-box">
        <h2>Admin Login</h2>
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label for="username">Username *</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit" class="btn primary-btn">Login</button>
        </form>
        <div class="back-home">
            <a href="index.php">← Back to website</a>
        </div>
    </div>
</div>
</body>
</html>
