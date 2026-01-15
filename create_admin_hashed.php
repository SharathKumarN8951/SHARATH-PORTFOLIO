<?php
require "config.php";

$username = "admin";
$plainPassword = "admin123";

$hashed = password_hash($plainPassword, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?) 
                        ON DUPLICATE KEY UPDATE password = VALUES(password)");
$stmt->bind_param("ss", $username, $hashed);

if ($stmt->execute()) {
    echo "Admin user created/updated with hashed password.<br>";
    echo "Username: " . htmlspecialchars($username) . "<br>";
    echo "Password: " . htmlspecialchars($plainPassword);
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
