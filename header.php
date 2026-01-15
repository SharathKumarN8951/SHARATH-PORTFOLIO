<?php
if (!isset($pageTitle)) {
    $pageTitle = "Sharath Kumar N | Portfolio";
}
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- IMPORTANT for proper mobile / iPhone responsiveness -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo htmlspecialchars($pageTitle); ?></title>

    <!-- Main stylesheet -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="header">
    <div class="container nav-container">
        <div class="logo">Sharath<span>Portfolio</span></div>

        <nav class="nav-links" id="navLinks">
            <a href="index.php"
               class="<?php echo $currentPage === 'index.php' ? 'active' : ''; ?>">
                Home
            </a>
            <a href="about.php"
               class="<?php echo $currentPage === 'about.php' ? 'active' : ''; ?>">
                About
            </a>
            <a href="skills.php"
               class="<?php echo $currentPage === 'skills.php' ? 'active' : ''; ?>">
                Skills
            </a>
            <a href="projects.php"
               class="<?php echo $currentPage === 'projects.php' ? 'active' : ''; ?>">
                Projects
            </a>
            <a href="resume.php"
               class="<?php echo $currentPage === 'resume.php' ? 'active' : ''; ?>">
                Resume
            </a>
            <a href="contact.php"
               class="<?php echo $currentPage === 'contact.php' ? 'active' : ''; ?>">
                Contact
            </a>
        </nav>

        <div class="right-controls">
            <!-- <a href="admin_login.php" class="admin-btn"></a> -->
            <button id="themeToggle" class="theme-toggle" type="button">🌙</button>
            <button id="menuToggle" class="menu-toggle" type="button">☰</button>
        </div>
    </div>
</header>
