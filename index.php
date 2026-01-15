<?php
// index.php (Home page only)
$pageTitle = "Home | Sharath Kumar N";
require "config.php";

// Helper to read site_content values
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

// Load dynamic Home section content
$hero_title       = get_content($conn, 'hero_title', 'Sharath Kumar N');
$hero_subtitle    = get_content($conn, 'hero_subtitle', 'Aspiring Data Scientist & Web Developer');
$hero_description = get_content($conn, 'hero_description', 'I build data-driven solutions and modern web applications using Python, PHP, MySQL, and JavaScript.');
$btn1_text        = get_content($conn, 'hero_button1_text', 'View Projects');
$btn1_link        = get_content($conn, 'hero_button1_link', 'projects.php');
$btn2_text        = get_content($conn, 'hero_button2_text', 'Contact Me');
$btn2_link        = get_content($conn, 'hero_button2_link', 'contact.php');
$profile_image    = get_content($conn, 'profile_image', ''); // IMPORTANT

include "header.php";
?>
<section id="home" class="section hero-section">
    <!-- added .reveal so this whole block animates on scroll -->
    <div class="container hero-grid reveal">
        <!-- hero-text already has its own fade-in animation from CSS -->
        <div class="hero-text">
            <p class="tagline">Hello, I'm</p>
            <h1><?php echo htmlspecialchars($hero_title); ?></h1>
            <h2><?php echo htmlspecialchars($hero_subtitle); ?></h2>
            <p class="hero-desc">
                <?php echo htmlspecialchars($hero_description); ?>
            </p>
            <div class="hero-buttons">
                <a href="<?php echo htmlspecialchars($btn1_link); ?>" class="btn primary-btn">
                    <?php echo htmlspecialchars($btn1_text); ?>
                </a>
                <a href="<?php echo htmlspecialchars($btn2_link); ?>" class="btn outline-btn">
                    <?php echo htmlspecialchars($btn2_text); ?>
                </a>
            </div>
        </div>

        <div class="hero-photo">
            <div class="photo-circle">
                <?php if (!empty($profile_image)): ?>
                    <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="Sharath Photo">
                <?php else: ?>
                    <span>SK</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php include "footer.php"; ?>
