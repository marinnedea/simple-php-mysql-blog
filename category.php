<?php
require 'includes/config.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$category_id = (int)$_GET['id'];
$stmt = $db->prepare("SELECT * FROM posts WHERE category_id=? ORDER BY date DESC");
$stmt->bind_param('i', $category_id);
$stmt->execute();
$result = $stmt->get_result();

$cat_stmt = $db->prepare("SELECT name FROM categories WHERE id=?");
$cat_stmt->bind_param('i', $category_id);
$cat_stmt->execute();
$category = $cat_stmt->get_result()->fetch_assoc();
$cat_stmt->close();

$category_name = $category ? htmlspecialchars($category['name']) : 'Unknown Category';
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= $category_name ?> - Blog</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div id="branding">
                <h1>My Blog</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <div class="container">
        <h1><?= $category_name ?></h1>
        <?php while ($row = $result->fetch_assoc()): ?>
            <h2><a href="view_post.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?></a></h2>
            <p><?= htmlspecialchars($row['content']) ?></p>
            <p>Posted on: <?= $row['date'] ?></p>
        <?php endwhile; ?>
    </div>
    <footer>
        <p>My Blog &copy; 2024</p>
    </footer>
</body>
</html>
