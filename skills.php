<?php
$pageTitle = "Skills | Sharath Kumar N";
require "config.php";

$skills = [];
$resSkills = $conn->query("SELECT * FROM skills ORDER BY id ASC");
if ($resSkills && $resSkills->num_rows > 0) {
    while ($row = $resSkills->fetch_assoc()) {
        $skills[] = $row;
    }
}

include "header.php";
?>
<section class="section dark-section">
    <div class="container">
        <h2 class="section-title">Skills</h2>
        <div class="skills-grid">
            <?php if (!empty($skills)): ?>
                <?php foreach ($skills as $s): ?>
                    <div class="skill-card">
                        <h3><?php echo htmlspecialchars($s['category']); ?></h3>
                        <ul>
                            <?php
                            $items = array_map('trim', explode(',', $s['items']));
                            foreach ($items as $item):
                                if ($item === '') continue;
                            ?>
                                <li><?php echo htmlspecialchars($item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No skills defined yet.</p>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php include "footer.php"; ?>
