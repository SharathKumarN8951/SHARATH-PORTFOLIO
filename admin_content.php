<?php
// admin_content.php
require "config.php";
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
// only admin
if (empty($_SESSION['admin_logged_in']) || empty($_SESSION['admin_username'])) {
    header("Location: admin_login.php");
    exit;
}

$actionMsg = "";

// Helper: get content_value by key
function get_content($conn, $key, $default = "") {
    $stmt = $conn->prepare("SELECT content_value FROM site_content WHERE content_key = ? LIMIT 1");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $res = $stmt->get_result();
    $value = $default;
    if ($res && $res->num_rows === 1) {
        $row = $res->fetch_assoc();
        $value = $row['content_value'];
    }
    $stmt->close();
    return $value;
}

// Save changes
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // --- 1) HANDLE PROFILE IMAGE UPLOAD (if any) ---
    if (isset($_FILES['profile_image_upload']) && $_FILES['profile_image_upload']['error'] === UPLOAD_ERR_OK) {
        $tmpPath  = $_FILES['profile_image_upload']['tmp_name'];
        $fileName = $_FILES['profile_image_upload']['name'];

        $parts = explode('.', $fileName);
        $ext   = strtolower(end($parts));

        // allowed image types
        $allowedImageExts = ['jpg', 'jpeg', 'png'];

        if (in_array($ext, $allowedImageExts)) {
            $uploadDir = 'images/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $newFileName = 'profile_' . date('Ymd_His') . '.' . $ext;
            $destPath    = $uploadDir . $newFileName;

            if (move_uploaded_file($tmpPath, $destPath)) {
                // Save to POST so it gets written into site_content
                $_POST['profile_image'] = $destPath;
            } else {
                $actionMsg = "Profile photo upload failed while moving the file.";
            }
        } else {
            $actionMsg = "Invalid profile photo type. Only JPG, JPEG, PNG allowed.";
        }
    }

    // --- 2) HANDLE RESUME FILE UPLOAD (if any) ---
    if (isset($_FILES['resume_upload']) && $_FILES['resume_upload']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['resume_upload']['tmp_name'];
        $fileName    = $_FILES['resume_upload']['name'];

        $fileNameCmps  = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Allow only PDF
        $allowedExtensions = ['pdf'];

        if (in_array($fileExtension, $allowedExtensions)) {
            // Make sure resume folder exists: my_portfolio/resume/
            $uploadDir = 'resume/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Make unique file name
            $newFileName = 'resume_' . date('Ymd_His') . '.' . $fileExtension;
            $destPath    = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // Set POST value so it will be saved in site_content
                $_POST['resume_file'] = $destPath;
            } else {
                $actionMsg = "Resume upload failed while moving the file.";
            }
        } else {
            $actionMsg = "Invalid resume file type. Only PDF is allowed.";
        }
    }

    // --- 3) SAVE TEXT FIELDS TO site_content ---
    $fields = [
    'hero_title', 'hero_subtitle', 'hero_description',
    'hero_button1_text', 'hero_button1_link',
    'hero_button2_text', 'hero_button2_link',
    'profile_image',   // ✅ REQUIRED
    'about_p1', 'about_p2',
    'about_education1', 'about_education2',
    'about_education3','about_education4',
    'about_interests',
    'resume_text', 'resume_file',
    'contact_email', 'contact_location',
    'contact_linkedin', 'contact_github'
];



    foreach ($fields as $key) {
        $val = trim($_POST[$key] ?? '');
        $stmt = $conn->prepare("
            INSERT INTO site_content (content_key, content_value)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE content_value = VALUES(content_value)
        ");
        $stmt->bind_param("ss", $key, $val);
        $stmt->execute();
        $stmt->close();
    }

    // --- 4) SKILLS (same as before) ---
    if (isset($_POST['skill_category']) && isset($_POST['skill_items'])) {
        $categories = $_POST['skill_category'];
        $itemsArr   = $_POST['skill_items'];

        $conn->query("DELETE FROM skills");
        $stmtSkill = $conn->prepare("INSERT INTO skills (category, items) VALUES (?, ?)");
        for ($i = 0; $i < count($categories); $i++) {
            $cat = trim($categories[$i]);
            $it  = trim($itemsArr[$i]);
            if ($cat !== "" && $it !== "") {
                $stmtSkill->bind_param("ss", $cat, $it);
                $stmtSkill->execute();
            }
        }
        $stmtSkill->close();
    }

    if ($actionMsg === "") {
        $actionMsg = "Content updated successfully.";
    }
}

// Load content
$hero_title         = get_content($conn, 'hero_title', 'Sharath Kumar N');
$hero_subtitle      = get_content($conn, 'hero_subtitle', 'Aspiring Data Scientist & Web Developer');
$hero_description   = get_content($conn, 'hero_description', '');
$hero_button1_text  = get_content($conn, 'hero_button1_text', 'View Projects');
$hero_button1_link  = get_content($conn, 'hero_button1_link', 'projects.php');
$hero_button2_text  = get_content($conn, 'hero_button2_text', 'Contact Me');
$hero_button2_link  = get_content($conn, 'hero_button2_link', 'contact.php');
$profile_image      = get_content($conn, 'profile_image', '');

$about_p1        = get_content($conn, 'about_p1', '');
$about_p2        = get_content($conn, 'about_p2', '');

// Backward compatible: if about_education1 is empty, fall back to old about_education value
$about_education1 = get_content(
    $conn,
    'about_education1',
    get_content($conn, 'about_education', '') // old single field
);
$about_education2 = get_content($conn, 'about_education2', '');
$about_education3 = get_content($conn, 'about_education3', '');
$about_education4 = get_content($conn, 'about_education4', '');

$about_interests = get_content($conn, 'about_interests', '');


$resume_text        = get_content($conn, 'resume_text', '');
$resume_file        = get_content($conn, 'resume_file', 'resume/Sharath_Kumar_N_Resume.pdf');

$contact_email      = get_content($conn, 'contact_email', 'yourmail@example.com');
$contact_location   = get_content($conn, 'contact_location', 'Bengaluru, Karnataka, India');
$contact_linkedin   = get_content($conn, 'contact_linkedin', 'your-linkedin-profile');
$contact_github     = get_content($conn, 'contact_github', 'your-github-username');

// Load skills
$skills = [];
$resSkills = $conn->query("SELECT * FROM skills ORDER BY id ASC");
if ($resSkills && $resSkills->num_rows > 0) {
    while ($row = $resSkills->fetch_assoc()) {
        $skills[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Site Content</title>
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
        .admin-sections {
            display: grid;
            grid-template-columns: minmax(0, 1.4fr) minmax(0, 1.6fr);
            gap: 20px;
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
        .admin-card h3 {
            font-size: 14px;
            margin-top: 10px;
            margin-bottom: 6px;
            color: #facc15;
        }
        .admin-card label {
            display: block;
            font-size: 13px;
            margin-bottom: 4px;
        }
        .admin-card input[type="text"],
        .admin-card textarea {
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
            min-height: 60px;
        }
        .admin-card input:focus,
        .admin-card textarea:focus {
            outline: none;
            border-color: #38bdf8;
        }
        .skills-list-admin .skill-row {
            display: flex;
            gap: 8px;
            margin-bottom: 8px;
        }
        .skills-list-admin .skill-row input {
            flex: 1;
        }
        .small-note {
            font-size: 11px;
            color: #9ca3af;
        }
        @media (max-width: 900px) {
            .admin-sections {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <div class="admin-header">
        <div class="container">
            <h1>Manage Site Content</h1>
            <div>
                Logged in as: <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong>
                <a href="admin_dashboard.php">Projects</a>
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

        <form method="post" enctype="multipart/form-data">

            <div class="admin-sections">
                <!-- Left column: Hero + About -->
                <div>
                    <div class="admin-card">
                        <h2>Home / Hero Section</h2>

                        <label>Title</label>
                        <input type="text" name="hero_title" value="<?php echo htmlspecialchars($hero_title); ?>">

                        <label>Subtitle</label>
                        <input type="text" name="hero_subtitle" value="<?php echo htmlspecialchars($hero_subtitle); ?>">

                        <label>Description</label>
                        <textarea name="hero_description"><?php echo htmlspecialchars($hero_description); ?></textarea>

                        <h3>Profile Photo</h3>
                        <label>Current Image Path</label>
                        <input type="text" name="profile_image" value="<?php echo htmlspecialchars($profile_image); ?>">

                        <?php if (!empty($profile_image)): ?>
                            <div style="margin: 6px 0 10px 0;">
                                <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile Preview"
                                     style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:2px solid #38bdf8;">
                            </div>
                        <?php endif; ?>

                        <label>Upload New Profile Photo (JPG/PNG)</label>
                        <input type="file" name="profile_image_upload" accept="image/*">
                        <p class="small-note">When you upload and save, the Home page will use the new photo.</p>

                        <h3>Buttons</h3>
                        <label>Button 1 Text</label>
                        <input type="text" name="hero_button1_text" value="<?php echo htmlspecialchars($hero_button1_text); ?>">
                        <label>Button 1 Link</label>
                        <input type="text" name="hero_button1_link" value="<?php echo htmlspecialchars($hero_button1_link); ?>">

                        <label>Button 2 Text</label>
                        <input type="text" name="hero_button2_text" value="<?php echo htmlspecialchars($hero_button2_text); ?>">
                        <label>Button 2 Link</label>
                        <input type="text" name="hero_button2_link" value="<?php echo htmlspecialchars($hero_button2_link); ?>">
                    </div>

                    <div class="admin-card">
    <h2>About Section</h2>

    <label>Paragraph 1</label>
    <textarea name="about_p1"><?php echo htmlspecialchars($about_p1); ?></textarea>

    <label>Paragraph 2</label>
    <textarea name="about_p2"><?php echo htmlspecialchars($about_p2); ?></textarea>

    <h3>Education (up to 3 entries)</h3>
    <p class="small-note">Example: B.Sc Computer Science - ABC College (2020)</p>

    <label>Education 1</label>
    <input type="text" name="about_education1"
           value="<?php echo htmlspecialchars($about_education1); ?>">

    <label>Education 2</label>
    <input type="text" name="about_education2"
           value="<?php echo htmlspecialchars($about_education2); ?>">

    <label>Education 3</label>
    <input type="text" name="about_education3"
           value="<?php echo htmlspecialchars($about_education3); ?>">

    
    <label>Education 4</label>
    <input type="text" name="about_education4"
           value="<?php echo htmlspecialchars($about_education4); ?>">

    <label>Interests</label>
    <input type="text" name="about_interests"
           value="<?php echo htmlspecialchars($about_interests); ?>">
</div>

                </div>

                <!-- Right column: Skills + Resume + Contact -->
                <div>
                    <div class="admin-card">
                        <h2>Skills Section</h2>
                        <p class="small-note">Each row: Category + comma-separated skills.</p>
                        <div class="skills-list-admin">
                            <?php
                            $rowsCount = max(count($skills), 3); // show at least 3 rows
                            for ($i = 0; $i < $rowsCount; $i++):
                                $catVal  = $skills[$i]['category'] ?? '';
                                $itemVal = $skills[$i]['items'] ?? '';
                            ?>
                                <div class="skill-row">
                                    <input type="text" name="skill_category[]" placeholder="Category (e.g. Programming)" value="<?php echo htmlspecialchars($catVal); ?>">
                                    <input type="text" name="skill_items[]" placeholder="Items (comma separated)" value="<?php echo htmlspecialchars($itemVal); ?>">
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <div class="admin-card">
                        <h2>Resume Section</h2>

                        <label>Resume Text</label>
                        <textarea name="resume_text"><?php echo htmlspecialchars($resume_text); ?></textarea>

                        <label>Current Resume File Path</label>
                        <input type="text" name="resume_file" value="<?php echo htmlspecialchars($resume_file); ?>">

                        <?php if (!empty($resume_file)): ?>
                            <div style="margin-top: 6px; margin-bottom: 8px;">
                                <a href="<?php echo htmlspecialchars($resume_file); ?>" target="_blank" class="btn outline-btn small-btn">
                                    View Current Resume
                                </a>
                            </div>
                        <?php endif; ?>

                        <p class="small-note">
                            This path is used on the Resume page. It will update automatically when you upload a new file.
                        </p>

                        <label>Upload New Resume (PDF)</label>
                        <input type="file" name="resume_upload" accept="application/pdf">
                        <p class="small-note">
                            After uploading and saving, the download button on the Resume page will use the new file.
                        </p>
                    </div>

                    <div class="admin-card">
                        <h2>Contact Section</h2>
                        <label>Email</label>
                        <input type="text" name="contact_email" value="<?php echo htmlspecialchars($contact_email); ?>">

                        <label>Location</label>
                        <input type="text" name="contact_location" value="<?php echo htmlspecialchars($contact_location); ?>">

                        <label>LinkedIn</label>
                        <input type="text" name="contact_linkedin" value="<?php echo htmlspecialchars($contact_linkedin); ?>">

                        <label>GitHub</label>
                        <input type="text" name="contact_github" value="<?php echo htmlspecialchars($contact_github); ?>">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn primary-btn" style="margin-top: 10px;">Save All Changes</button>
        </form>
    </div>
</div>
</body>
</html>
