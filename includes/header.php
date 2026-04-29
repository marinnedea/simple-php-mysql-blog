<?php
// Shared public header.
// $page_title — optional, prepended to SITE_TITLE in the <title> tag.
if (session_status() === PHP_SESSION_NONE) session_start();
$title = isset($page_title) ? htmlspecialchars($page_title) . ' - ' . SITE_TITLE : SITE_TITLE;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <?php if (SITE_FAVICON): ?>
        <link rel="icon" href="<?= SITE_FAVICON ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="<?= $css_path ?? 'css/style.css' ?>">
</head>
<body>
    <header>
        <div class="container">
            <div id="branding">
                <?php if (SITE_LOGO): ?>
                    <a href="index.php"><img src="<?= SITE_LOGO ?>" alt="<?= SITE_TITLE ?>" class="site-logo"></a>
                <?php else: ?>
                    <a href="index.php" class="site-title"><?= htmlspecialchars(SITE_TITLE) ?></a>
                <?php endif; ?>
                <?php if (SITE_SUBTITLE): ?>
                    <span class="site-subtitle"><?= htmlspecialchars(SITE_SUBTITLE) ?></span>
                <?php endif; ?>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <?php if (!empty($_SESSION['loggedin'])): ?>
                        <li><a href="admin/admin.php" class="nav-admin">Admin Area</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
