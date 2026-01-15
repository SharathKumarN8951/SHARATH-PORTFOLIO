<?php
$pageTitle = "Resume | Sharath Kumar N";
require "config.php";

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

$resume_text = get_content($conn, 'resume_text', '');
$resume_file = get_content($conn, 'resume_file', 'resume/Sharath_Kumar_N_Resume.pdf');

include "header.php";
?>
<section class="section">
    <div class="container">
        <h2 class="section-title">Resume</h2>
        <div class="resume-card">
            <p><?php echo htmlspecialchars($resume_text); ?></p>
            <a href="<?php echo htmlspecialchars($resume_file); ?>" class="btn primary-btn" download>
                Download Resume
            </a>
        </div>
    </div>
</section>
<?php include "footer.php"; ?>
