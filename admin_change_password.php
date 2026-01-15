<?php
// admin_change_password.php
require "config.php";
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
// Only logged-in admins can access
if (empty($_SESSION['admin_logged_in']) || empty($_SESSION['admin_username'])) {
    header("Location: admin_login.php");
    exit;
}

$username = $_SESSION['admin_username'];
$successMsg = "";
$errorMsg   = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $current = trim($_POST['current_password'] ?? '');
    $new     = trim($_POST['new_password'] ?? '');
    $confirm = trim($_POST['confirm_password'] ?? '');

    // Basic validation
    if ($current === "" || $new === "" || $confirm === "") {
        $errorMsg = "Please fill all fields.";
    } elseif ($new !== $confirm) {
        $errorMsg = "New password and confirm password do not match.";
    } elseif (strlen($new) < 8) {
        $errorMsg = "New password should be at least 8 characters long.";
    } else {
        // Get current hashed password from DB
        $stmt = $conn->prepare("SELECT password FROM admin_users WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows === 1) {
            $row = $res->fetch_assoc();
            $hashed = $row['password'];

            // Verify current password
            if (!password_verify($current, $hashed)) {
                $errorMsg = "Current password is incorrect.";
            } else {
                // Hash new password and update
                $newHashed = password_hash($new, PASSWORD_DEFAULT);
                $stmtUpdate = $conn->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
                $stmtUpdate->bind_param("ss", $newHashed, $username);

                if ($stmtUpdate->execute()) {
                    $successMsg = "Password changed successfully.";
                } else {
                    $errorMsg = "Failed to update password. Please try again.";
                }
                $stmtUpdate->close();
            }
        } else {
            $errorMsg = "Admin user not found.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password | Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-wrapper {
            min-height: 100vh;
            background: #020617;
            color: #e5e7eb;
            padding-bottom: 40px;
        }
        .admin-header {
            padding: 15px 0;
            border-bottom: 1px solid #1f2937;
            margin-bottom: 20px;
        }
        .admin-header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-header h1 {
            font-size: 20px;
        }
        .admin-header a {
            font-size: 13px;
            color: #f97316;
            text-decoration: none;
            margin-left: 10px;
        }
        .admin-container {
            width: 90%;
            max-width: 600px;
            margin: 0 auto;
        }
        .admin-card {
            background: #020617;
            border-radius: 16px;
            border: 1px solid #1f2937;
            padding: 18px;
            font-size: 13px;
        }
        .admin-card h2 {
            font-size: 16px;
            margin-bottom: 10px;
            color: #38bdf8;
        }
        .msg-success {
            background: #14532d;
            color: #bbf7d0;
            padding: 8px 10px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 10px;
        }
        .msg-error {
            background: #7f1d1d;
            color: #fee2e2;
            padding: 8px 10px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 10px;
        }
        .admin-card .form-group {
            margin-bottom: 12px;
        }
        .admin-card label {
            display: block;
            font-size: 13px;
            margin-bottom: 4px;
        }
        .admin-card input[type="password"] {
            width: 100%;
            padding: 8px 10px;
            border-radius: 8px;
            border: 1px solid #4b5563;
            background: #020617;
            color: #e5e7eb;
            font-size: 13px;
        }
        .admin-card input[type="password"]:focus {
            outline: none;
            border-color: #38bdf8;
        }
        .admin-card button {
            margin-top: 6px;
        }
        @media (max-width: 768px) {
            .admin-header .container {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
        }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <div class="admin-header">
        <div class="container">
            <a href="admin_dashboard.php" class="admin-back-btn">← Back to Dashboard</a>

            <h1>Change Password</h1>
            <div>
    Logged in as: <strong><?php echo htmlspecialchars($username); ?></strong>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="admin_content.php">Site Content</a>
    <a href="admin_messages.php">Messages</a>
    <a href="index.php" target="_blank">View Site</a>
    <a href="logout.php">Logout</a>
</div>

        </div>
    </div>

    <div class="admin-container">
        <div class="admin-card">
            <h2>Update Your Password</h2>

            <?php if (!empty($successMsg)): ?>
                <div class="msg-success"><?php echo htmlspecialchars($successMsg); ?></div>
            <?php endif; ?>

            <?php if (!empty($errorMsg)): ?>
                <div class="msg-error"><?php echo htmlspecialchars($errorMsg); ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="form-group">
                    <label for="current_password">Current Password *</label>
                    <input type="password" name="current_password" id="current_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">New Password *</label>
                    <input type="password" name="new_password" id="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password *</label>
                    <input type="password" name="confirm_password" id="confirm_password" required>
                </div>
                <button type="submit" class="btn primary-btn">Change Password</button>
                <p class="form-note">Password should be at least 8 characters.</p>
            </form>
        </div>
    </div>
</div>
</body>
</html>
