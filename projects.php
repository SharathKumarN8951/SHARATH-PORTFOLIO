<?php
$pageTitle = "Projects | Sharath Kumar N";
require "config.php";

/* Pagination setup */
$projectsPerPage = 6;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($currentPage < 1) $currentPage = 1;
$offset = ($currentPage - 1) * $projectsPerPage;

// total projects
$countRes = $conn->query("SELECT COUNT(*) AS cnt FROM projects");
$countRow = $countRes->fetch_assoc();
$totalProjects = (int)$countRow['cnt'];
$totalPages = $totalProjects > 0 ? ceil($totalProjects / $projectsPerPage) : 1;

// fetch projects for this page
$projects = [];
// we select * so image_path, github_url, live_url are also available
$stmtProj = $conn->prepare("SELECT * FROM projects ORDER BY id DESC LIMIT ?, ?");
$stmtProj->bind_param("ii", $offset, $projectsPerPage);
$stmtProj->execute();
$resProj = $stmtProj->get_result();
if ($resProj && $resProj->num_rows > 0) {
    while ($row = $resProj->fetch_assoc()) {
        $projects[] = $row;
    }
}
$stmtProj->close();

include "header.php";
?>
<section class="section">
    <div class="container">
        <h2 class="section-title">Projects</h2>

        <div class="projects-search">
            <input type="text" id="projectSearch"
                   placeholder="Search by title or tech (e.g., PHP, ML, MySQL)">
        </div>

        <div class="projects-grid" id="projectsGrid">
            <?php if (!empty($projects)): ?>
                <?php foreach ($projects as $project): ?>
                    <div class="project-card"
                         data-title="<?php echo htmlspecialchars(strtolower($project['title'])); ?>"
                         data-tech="<?php echo htmlspecialchars(strtolower($project['tech_stack'])); ?>">

                        <!-- Project Image (from admin upload) -->
                        <?php if (!empty($project['image_path'])): ?>
                            <div style="margin-bottom:10px;">
                                <img src="<?php echo htmlspecialchars($project['image_path']); ?>"
                                     alt="Project Image"
                                     style="width:100%;border-radius:12px;object-fit:cover;max-height:180px;">
                            </div>
                        <?php endif; ?>

                        <h3><?php echo htmlspecialchars($project['title']); ?></h3>

                        <p><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>

                        <p class="project-tech">
                            Tech: <?php echo htmlspecialchars($project['tech_stack']); ?>
                        </p>

                        <?php
                        // New link logic: GitHub + Live (from columns github_url, live_url)
                        $githubUrl = $project['github_url'] ?? '';
                        $liveUrl   = $project['live_url'] ?? '';
                        ?>

                        <?php if (!empty($githubUrl) || !empty($liveUrl)): ?>
                            <p style="margin-top:8px;">
                                <?php if (!empty($githubUrl)): ?>
                                    <a href="<?php echo htmlspecialchars($githubUrl); ?>"
                                       class="project-link" target="_blank">
                                        GitHub
                                    </a>
                                <?php endif; ?>

                                <?php if (!empty($liveUrl)): ?>
                                    <?php if (!empty($githubUrl)) echo " | "; ?>
                                    <a href="<?php echo htmlspecialchars($liveUrl); ?>"
                                       class="project-link" target="_blank">
                                        Live Demo
                                    </a>
                                <?php endif; ?>
                            </p>
                        <?php else: ?>
                            <span class="project-link disabled">Demo coming soon</span>
                        <?php endif; ?>

                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align:center;">No projects added yet.</p>
            <?php endif; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="projects-pagination">
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <?php if ($p == $currentPage): ?>
                        <span class="current-page"><?php echo $p; ?></span>
                    <?php else: ?>
                        <a href="projects.php?page=<?php echo $p; ?>"><?php echo $p; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php include "footer.php"; ?>
