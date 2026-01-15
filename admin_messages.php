<?php
// admin_messages.php
require "config.php";
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
// check login
if (empty($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

$actionMsg = "";

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $actionMsg = "Message deleted successfully.";
    } else {
        $actionMsg = "Failed to delete message.";
    }
    $stmt->close();
}

// Pagination for messages
$perPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $perPage;

// Total messages
$totalRes = $conn->query("SELECT COUNT(*) AS cnt FROM contact_messages");
$totalRow = $totalRes->fetch_assoc();
$totalMessages = (int)$totalRow['cnt'];
$totalPages = $totalMessages > 0 ? ceil($totalMessages / $perPage) : 1;

// Fetch messages (latest first)
$stmt = $conn->prepare("SELECT id, name, email, subject, message, created_at 
                        FROM contact_messages 
                        ORDER BY created_at DESC 
                        LIMIT ?, ?");
$stmt->bind_param("ii", $offset, $perPage);
$stmt->execute();
$result = $stmt->get_result();
$messages = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Contact Messages</title>
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
            max-width: 1100px;
            margin: 0 auto;
        }
        .admin-msg {
            background: #1d4ed8;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 12px;
        }
        .admin-card {
            background: #020617;
            border-radius: 16px;
            border: 1px solid #1f2937;
            padding: 16px;
            font-size: 13px;
        }
        .admin-card h2 {
            font-size: 16px;
            margin-bottom: 10px;
            color: #38bdf8;
        }
        .messages-table {
            width: 100%;
            border-collapse: collapse;
        }
        .messages-table th,
        .messages-table td {
            border-bottom: 1px solid #1f2937;
            padding: 8px 6px;
            vertical-align: top;
        }
        .messages-table th {
            font-size: 12px;
            text-align: left;
            color: #a5b4fc;
        }
        .messages-table td {
            font-size: 12px;
        }
        .msg-meta {
            font-size: 11px;
            color: #9ca3af;
        }
        .msg-actions a,
        .msg-actions button {
            font-size: 11px;
            color: #f97316;
            text-decoration: none;
            margin-right: 6px;
            background: none;
            border: 1px solid #f97316;
            border-radius: 999px;
            padding: 3px 8px;
            cursor: pointer;
        }
        .msg-actions a:hover,
        .msg-actions button:hover {
            background: #f97316;
            color: #020617;
        }
        .msg-actions button {
            color: #38bdf8;
            border-color: #38bdf8;
        }
        .msg-actions button:hover {
            background: #38bdf8;
            color: #020617;
        }
        .messages-pagination {
            margin-top: 12px;
            text-align: right;
            font-size: 12px;
        }
        .messages-pagination a,
        .messages-pagination span {
            margin-left: 6px;
            text-decoration: none;
            color: #38bdf8;
        }
        .messages-pagination .current {
            font-weight: bold;
            color: #facc15;
        }
        @media (max-width: 768px) {
            .messages-table th:nth-child(2),
            .messages-table td:nth-child(2) {
                display: none; /* hide subject column on small screens if needed */
            }
        }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <div class="admin-header">
        <div class="container">
            <a href="admin_dashboard.php" class="admin-back-btn">← Back to Dashboard</a>

            <h1>Contact Messages</h1>
            <div>
                <span>Logged in as: <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong></span>
                <a href="admin_dashboard.php">Projects</a>
                <a href="admin_content.php">Site Content</a>
                <a href="admin_change_password.php">Change Password</a>
                <a href="index.php" target="_blank">View Site</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>

    <div class="admin-container">
        <?php if (!empty($actionMsg)): ?>
            <div class="admin-msg"><?php echo htmlspecialchars($actionMsg); ?></div>
        <?php endif; ?>

        <div class="admin-card">
            <h2>Messages (<?php echo $totalMessages; ?>)</h2>

            <?php if (!empty($messages)): ?>
                <table class="messages-table">
                    <thead>
                    <tr>
                        <th>Name / Email</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Received</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($messages as $m): ?>
                        <?php
                            // Prepare URLs
                            $gmailUrl = "https://mail.google.com/mail/?view=cm&fs=1"
                                . "&to=" . urlencode($m['email'])
                                . "&su=" . urlencode("Re: " . $m['subject'])
                                . "&body=" . urlencode("\n\n--- Original message ---\n" . $m['message']);

                            $mailtoUrl = "mailto:" . rawurlencode($m['email'])
                                . "?subject=" . rawurlencode("Re: " . $m['subject'])
                                . "&body=" . rawurlencode("\n\n--- Original message ---\n" . $m['message']);
                        ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($m['name']); ?></strong><br>
                                <span class="msg-meta"><?php echo htmlspecialchars($m['email']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($m['subject']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($m['message'])); ?></td>
                            <td class="msg-meta">
                                <?php echo htmlspecialchars($m['created_at']); ?>
                            </td>
                            <td class="msg-actions">
                                <!-- Reply via Gmail -->
                                <a href="<?php echo $gmailUrl; ?>" target="_blank">
                                    Reply Gmail
                                </a>

                                <!-- Reply via default mail app -->
                                <a href="<?php echo $mailtoUrl; ?>">
                                    Reply Mail App
                                </a>

                                <!-- Copy email button -->
                                <button type="button"
                                    onclick="copyEmail('<?php echo htmlspecialchars($m['email'], ENT_QUOTES); ?>')">
                                    Copy Email
                                </button>

                                <!-- Delete -->
                                <a href="admin_messages.php?delete=<?php echo (int)$m['id']; ?>"
                                   onclick="return confirm('Delete this message?');">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if ($totalPages > 1): ?>
                    <div class="messages-pagination">
                        Page:
                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <?php if ($p == $page): ?>
                                <span class="current"><?php echo $p; ?></span>
                            <?php else: ?>
                                <a href="admin_messages.php?page=<?php echo $p; ?>"><?php echo $p; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <p>No messages received yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Copy email to clipboard
function copyEmail(email) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(email)
            .then(() => {
                alert("Email copied: " + email);
            })
            .catch(() => {
                alert("Failed to copy email.");
            });
    } else {
        // Fallback for old browsers
        const tempInput = document.createElement("input");
        tempInput.value = email;
        document.body.appendChild(tempInput);
        tempInput.select();
        try {
            document.execCommand("copy");
            alert("Email copied: " + email);
        } catch (e) {
            alert("Failed to copy email.");
        }
        document.body.removeChild(tempInput);
    }
}
</script>
</body>
</html>
