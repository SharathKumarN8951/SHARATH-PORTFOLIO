<?php
$pageTitle = "About | Sharath Kumar N";
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

/* About paragraphs */
$about_p1 = get_content($conn, 'about_p1', '');
$about_p2 = get_content($conn, 'about_p2', '');

/* New multi-education fields */
$about_education1 = get_content($conn, 'about_education1', '');
$about_education2 = get_content($conn, 'about_education2', '');
$about_education3 = get_content($conn, 'about_education3', '');
$about_education4 = get_content($conn, 'about_education4', '');

/* Old single education (for backward compatibility) */
$legacy_education = get_content($conn, 'about_education', '');

/* If new fields empty but old one exists, use it as first entry */
if (
    $about_education1 === '' &&
    $about_education2 === '' &&
    $about_education3 === '' &&
    $about_education4 === '' &&
    $legacy_education !== ''
) {
    $about_education1 = $legacy_education;
}



/**
 * Helper: parse one education string into title + lines.
 * Format you can type in admin:
 *   "BCA | ABC College, Bengaluru | 2021 - 2024 | CGPA: 8.5"
 */
function parse_edu_entry($str) {
    $str = trim($str);
    if ($str === '') return null;

    $parts = array_map('trim', explode('|', $str));

    $title = array_shift($parts);   // first part = title
    $lines = $parts;                // remaining parts = small lines

    return [
        'title' => $title,
        'lines' => $lines
    ];
}

/* Build list of education items */
$education_items = [];
foreach ([$about_education1, $about_education2, $about_education3,$about_education4] as $eduStr) {
    $parsed = parse_edu_entry($eduStr);
    if ($parsed !== null) {
        $education_items[] = $parsed;
    }
}

/* Interests */
$about_interests = get_content($conn, 'about_interests', '');

include "header.php";
?>
<section class="section">
    <div class="container">
        <h2 class="section-title">About Me</h2>

        <div class="about-grid">
            <!-- Left: About text -->
            <div class="about-text">
                <p><?php echo htmlspecialchars($about_p1); ?></p>
                <p><?php echo htmlspecialchars($about_p2); ?></p>
            </div>

            <!-- Right: Education cards + Interests -->
            <div class="about-card">
                <h3 class="edu-section-title">Education</h3>

                <?php if (!empty($education_items)): ?>
                    <div class="education-list">
                        <?php foreach ($education_items as $item): ?>
                            <div class="education-card">
                                <div class="edu-icon">
                                    🎓
                                </div>
                                <div class="edu-content">
                                    <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                                    <?php if (!empty($item['lines'])): ?>
                                        <?php foreach ($item['lines'] as $line): ?>
                                            <p class="edu-line"><?php echo htmlspecialchars($line); ?></p>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <!-- Fallback: show old single education field if nothing parsed -->
                    <p><?php echo htmlspecialchars($legacy_education); ?></p>
                <?php endif; ?>

                <h3 style="margin-top: 18px;">Interests</h3>
                <p><?php echo htmlspecialchars($about_interests); ?></p>
            </div>
        </div>
    </div>
</section>
<?php include "footer.php"; ?>
