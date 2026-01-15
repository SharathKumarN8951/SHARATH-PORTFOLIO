<?php
$pageTitle = "Contact | Sharath Kumar N";
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

$contact_email    = get_content($conn, 'contact_email', 'yourmail@example.com');
$contact_location = get_content($conn, 'contact_location', 'Bengaluru, Karnataka, India');
$contact_linkedin = get_content($conn, 'contact_linkedin', 'your-linkedin-profile');
$contact_github   = get_content($conn, 'contact_github', 'your-github-username');

$statusMsg = "";
if (isset($_GET['status']) && $_GET['status'] === "success") {
    $statusMsg = "✅ Thank you! Your message has been sent.";
} elseif (isset($_GET['status']) && $_GET['status'] === "error") {
    $statusMsg = "❌ Something went wrong. Please try again.";
}

include "header.php";
?>
<section class="section dark-section">
    <div class="container">
        <h2 class="section-title">Contact Me</h2>

        <?php if (!empty($statusMsg)): ?>
            <div class="status-message">
                <?php echo htmlspecialchars($statusMsg); ?>
            </div>
        <?php endif; ?>

        <div class="contact-grid">
            <form action="contact_process.php" method="post" class="contact-form" id="contactForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Name *</label>
                        <input type="text" name="name" id="name" required>
                    </div>
                    <div class="form-group">
                      <label for="email">Email *</label>
<input
    type="text"
    name="email"
    id="email"
    required
    pattern="^[a-z0-9._%+-]+@gmail\.com$"
    title="Use only small letters. Example: sharath123@gmail.com"
>
<span id="emailError" style="color:#f87171;font-size:12px;"></span>

 
                    </div>
                </div>
                <div class="form-group">
                    <label for="subject">Subject *</label>
                    <input type="text" name="subject" id="subject" required>
                </div>
                <div class="form-group">
                    <label for="message">Message *</label>
                    <textarea name="message" id="message" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn primary-btn">Send Message</button>
                <p class="form-note">* All fields are required</p>
            </form>

            <div class="contact-info">
                <h3>Contact Details</h3>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($contact_email); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($contact_location); ?></p>
                 <p><strong>
    LinkedIn:</strong> 
    <a href="https://www.linkedin.com/in/sharath-kumar-n-9b3662313?utm_source=share_via&utm_content=profile&utm_medium=member_android" 
       target="_blank" 
       class="contact-link">
       linkedin.com/in/sharathkumar
    </a>
</p>
                <p><strong> 
    Instagram:</strong> 
    <a href="https://www.instagram.com/sharath___gowda___07?igsh=MXA0cWxubTlsN3Vsbw==" 
       target="_blank" 
       class="contact-link instagram-link">
       sharath__gowda__o7
    </a>
</p>
                <p><strong>GitHub:</strong> <?php echo htmlspecialchars($contact_github); ?></p>
            </div>
        </div>
    </div>
</section>
<?php include "footer.php"; ?>
