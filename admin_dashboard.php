<?php
// admin_dashboard.php
require "config.php";
session_start();

/* 🔒 No cache so Back button can't show after logout */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

/* 🔐 Only admin */
if (empty($_SESSION['admin_logged_in']) || empty($_SESSION['admin_username'])) {
    header("Location: admin_login.php");
    exit;
}

$actionMsg   = "";
$editMode    = false;
$editProject = null;

/* ===========================
   IF EDIT LINK CLICKED
   =========================== */
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    if ($editId > 0) {
        $stmt = $conn->prepare("SELECT id, title, tech_stack, image_path, description, github_url, live_url FROM projects WHERE id = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param("i", $editId);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res && $res->num_rows === 1) {
                $editProject = $res->fetch_assoc();
                $editMode = true;
            }
            $stmt->close();
        }
    }
}

/* ===========================
   ADD / UPDATE PROJECT
   =========================== */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action'])) {
    $action      = $_POST['action'];
    $title       = trim($_POST['title'] ?? '');
    $tech_stack  = trim($_POST['tech_stack'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $github_url  = trim($_POST['github_url'] ?? '');
    $live_url    = trim($_POST['live_url'] ?? '');
    $image_path  = trim($_POST['old_image_path'] ?? ''); // for update or default

    // Handle project image upload (optional)
    if (isset($_FILES['project_image']) && $_FILES['project_image']['error'] === UPLOAD_ERR_OK) {
        $tmpPath  = $_FILES['project_image']['tmp_name'];
        $fileName = $_FILES['project_image']['name'];

        $parts = explode('.', $fileName);
        $ext   = strtolower(end($parts));

        $allowedImageExts = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $allowedImageExts)) {
            $uploadDir = 'project_images/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $newFileName = 'project_' . date('Ymd_His') . '_' . mt_rand(1000, 9999) . '.' . $ext;
            $destPath    = $uploadDir . $newFileName;

            if (move_uploaded_file($tmpPath, $destPath)) {
                // Remove old image if exists (for edit mode)
                if (!empty($image_path) && file_exists($image_path)) {
                    @unlink($image_path);
                }
                $image_path = $destPath;
            } else {
                $actionMsg = "Project image upload failed while moving the file.";
            }
        } else {
            $actionMsg = "Invalid project image type. Only JPG, JPEG, PNG, WEBP allowed.";
        }
    }

    if ($title === "" || $description === "") {
        $actionMsg = "Title and Description are required.";
    } else {
        // ADD NEW
        if ($action === 'add') {
            $stmt = $conn->prepare("
                INSERT INTO projects (title, tech_stack, image_path, description, github_url, live_url)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            if ($stmt) {
                $stmt->bind_param("ssssss", $title, $tech_stack, $image_path, $description, $github_url, $live_url);
                if ($stmt->execute()) {
                    $actionMsg = "Project added successfully.";
                } else {
                    $actionMsg = "Failed to add project.";
                }
                $stmt->close();
            } else {
                $actionMsg = "Database error while preparing project insert.";
            }
        }

        // UPDATE EXISTING
        if ($action === 'update') {
            $project_id = (int)($_POST['project_id'] ?? 0);

            if ($project_id > 0) {
                $stmt = $conn->prepare("
                    UPDATE projects
                    SET title = ?, tech_stack = ?, image_path = ?, description = ?, github_url = ?, live_url = ?
                    WHERE id = ?
                ");
                if ($stmt) {
                    $stmt->bind_param("ssssssi", $title, $tech_stack, $image_path, $description, $github_url, $live_url, $project_id);
                    if ($stmt->execute()) {
                        $actionMsg = "Project updated successfully.";
                    } else {
                        $actionMsg = "Failed to update project.";
                    }
                    $stmt->close();
                } else {
                    $actionMsg = "Database error while preparing update.";
                }
            } else {
                $actionMsg = "Invalid project ID for update.";
            }

            // After update, remove ?edit= from URL
            header("Location: admin_dashboard.php");
            exit;
        }
    }
}

/* ===========================
   DELETE PROJECT
   =========================== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Get old image to delete file
    $oldImage = "";
    $res = $conn->prepare("SELECT image_path FROM projects WHERE id = ?");
    if ($res) {
        $res->bind_param("i", $id);
        $res->execute();
        $resResult = $res->get_result();
        if ($resResult && $resResult->num_rows === 1) {
            $row = $resResult->fetch_assoc();
            $oldImage = $row['image_path'] ?? "";
        }
        $res->close();
    }

    $stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            if ($oldImage && file_exists($oldImage)) {
                @unlink($oldImage);
            }
            $actionMsg = "Project deleted successfully.";
        } else {
            $actionMsg = "Failed to delete project.";
        }
        $stmt->close();
    } else {
        $actionMsg = "Database error while preparing delete.";
    }
}

/* ===========================
   FETCH ALL PROJECTS
   =========================== */
$projects = [];
$result = $conn->query("SELECT id, title, tech_stack, image_path, description, github_url, live_url FROM projects ORDER BY id DESC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Projects</title>
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
        .admin-flex {
            display: grid;
            grid-template-columns: minmax(0, 1.3fr) minmax(0, 1.7fr);
            gap: 18px;
        }
        .admin-card {
            background: #020617;
            border-radius: 16px;
            border: 1px solid #1f2937;
            padding: 16px;
            font-size: 13px;
            margin-bottom: 18px;
        }
        .admin-card h2 {
            font-size: 16px;
            margin-bottom: 10px;
            color: #38bdf8;
        }
        .admin-card label {
            display: block;
            font-size: 13px;
            margin-bottom: 4px;
        }
        .admin-card input[type="text"],
        .admin-card textarea,
        .admin-card input[type="file"] {
            width: 100%;
            padding: 8px 10px;
            border-radius: 8px;
            border: 1px solid #4b5563;
            background: #020617;
            color: #e5e7eb;
            font-size: 13px;
            margin-bottom: 8px;
        }
        .admin-card textarea {
            min-height: 70px;
        }
        .admin-card input:focus,
        .admin-card textarea:focus {
            outline: none;
            border-color: #38bdf8;
        }
        .projects-table {
            width: 100%;
            border-collapse: collapse;
        }
        .projects-table th,
        .projects-table td {
            border-bottom: 1px solid #1f2937;
            padding: 8px 6px;
            vertical-align: top;
            font-size: 12px;
        }
        .projects-table th {
            color: #a5b4fc;
        }
        .project-thumb {
            width: 70px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #1f2937;
        }
        .project-actions a {
            font-size: 11px;
            color: #f97316;
            text-decoration: none;
        }
        .msg-meta {
            font-size: 11px;
            color: #9ca3af;
        }
        @media (max-width: 900px) {
            .admin-flex {
                grid-template-columns: 1fr;
            }
            .projects-table th:nth-child(3),
            .projects-table td:nth-child(3) {
                display: none; /* hide description on small screens */
            }
        }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <div class="admin-header">
        <div class="container">
            <h1>Admin - Projects</h1>
            <div>
                Logged in as: <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong>
                <a href="admin_content.php">Site Content</a>
                <a href="admin_messages.php">Messages</a>
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

        <div class="admin-flex">
            <!-- Left: Add / Edit Project -->
            <div class="admin-card">
                <?php if ($editMode && $editProject): ?>
                    <h2>Edit Project</h2>
                <?php else: ?>
                    <h2>Add New Project</h2>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="<?php echo $editMode ? 'update' : 'add'; ?>">

                    <?php if ($editMode && $editProject): ?>
                        <input type="hidden" name="project_id" value="<?php echo (int)$editProject['id']; ?>">
                        <input type="hidden" name="old_image_path" value="<?php echo htmlspecialchars($editProject['image_path']); ?>">
                    <?php else: ?>
                        <input type="hidden" name="old_image_path" value="">
                    <?php endif; ?>

                    <label>Title *</label>
                    <input type="text" name="title" required
                           value="<?php echo $editMode ? htmlspecialchars($editProject['title']) : ''; ?>">

                    <label>Tech Stack (comma separated)</label>
                    <input type="text" name="tech_stack" placeholder="PHP, MySQL, HTML, CSS"
                           value="<?php echo $editMode ? htmlspecialchars($editProject['tech_stack']) : ''; ?>">

                    <label>Description *</label>
                    <textarea name="description" required><?php
                        echo $editMode ? htmlspecialchars($editProject['description']) : '';
                    ?></textarea>

                    <label>GitHub URL</label>
                    <input type="text" name="github_url" placeholder="https://github.com/..."
                           value="<?php echo $editMode ? htmlspecialchars($editProject['github_url']) : ''; ?>">

                    <label>Live URL</label>
                    <input type="text" name="live_url" placeholder="http://localhost/... or https://..."
                           value="<?php echo $editMode ? htmlspecialchars($editProject['live_url']) : ''; ?>">

                    <?php if ($editMode && $editProject && !empty($editProject['image_path'])): ?>
                        <label>Current Image</label>
                        <div style="margin-bottom:8px;">
                            <img src="<?php echo htmlspecialchars($editProject['image_path']); ?>"
                                 alt="Current Project Image" class="project-thumb">
                        </div>
                        <label>Change Image (optional)</label>
                        <input type="file" name="project_image" accept="image/*">
                    <?php else: ?>
                        <label>Project Image (optional, JPG/PNG/WEBP)</label>
                        <input type="file" name="project_image" accept="image/*">
                    <?php endif; ?>

                    <button type="submit" class="btn primary-btn" style="margin-top: 8px;">
                        <?php echo $editMode ? 'Update Project' : 'Add Project'; ?>
                    </button>

                    <?php if ($editMode): ?>
                        <a href="admin_dashboard.php" class="btn outline-btn" style="margin-top: 8px; margin-left:6px;">
                            Cancel Edit
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Right: Existing Projects (list + separate Edit & Delete columns) -->
            <div class="admin-card">
                <h2>Existing Projects</h2>
                <?php if (!empty($projects)): ?>
                    <table class="projects-table">
                        <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title / Tech</th>
                            <th>Description</th>
                            <th>Links</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($projects as $p): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($p['image_path'])): ?>
                                        <img src="<?php echo htmlspecialchars($p['image_path']); ?>"
                                             alt="Project Image" class="project-thumb">
                                    <?php else: ?>
                                        <span class="msg-meta">No Image</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($p['title']); ?></strong><br>
                                    <span class="msg-meta">
                                        <?php echo htmlspecialchars($p['tech_stack']); ?>
                                    </span>
                                </td>
                                <td><?php echo nl2br(htmlspecialchars($p['description'])); ?></td>
                                <td>
                                    <?php if (!empty($p['github_url'])): ?>
                                        <a href="<?php echo htmlspecialchars($p['github_url']); ?>" target="_blank">GitHub</a><br>
                                    <?php endif; ?>
                                    <?php if (!empty($p['live_url'])): ?>
                                        <a href="<?php echo htmlspecialchars($p['live_url']); ?>" target="_blank">Live</a>
                                    <?php endif; ?>
                                </td>
                                <td class="project-actions">
                                    <a href="admin_dashboard.php?edit=<?php echo (int)$p['id']; ?>">
                                        Edit
                                    </a>
                                </td>
                                <td class="project-actions">
                                    <a href="admin_dashboard.php?delete=<?php echo (int)$p['id']; ?>"
                                       onclick="return confirm('Delete this project?');">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No projects added yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
