<?php
require 'includes/config.php';

$categories = $db->query("SELECT id, name FROM categories ORDER BY name");
$posts = $db->query("
    SELECT p.id, p.title, p.content, p.date, c.name AS category, c.id AS category_id
    FROM posts p
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.date DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div id="branding"><h1>My Blog</h1></div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <h1>Blog Posts</h1>

        <div class="categories">
            <?php while ($cat = $categories->fetch_assoc()): ?>
                <a href="category.php?id=<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a>
            <?php endwhile; ?>
        </div>

        <?php if ($posts->num_rows === 0): ?>
            <p>No posts yet.</p>
        <?php else: ?>
            <?php while ($row = $posts->fetch_assoc()): ?>
                <div class="post-item">
                    <h2><a href="view_post.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?></a></h2>
                    <div class="meta">
                        <?= $row['date'] ?>
                        <?php if ($row['category']): ?>
                            &nbsp;·&nbsp;
                            <a href="category.php?id=<?= $row['category_id'] ?>"><?= htmlspecialchars($row['category']) ?></a>
                        <?php endif; ?>
                    </div>
                    <p><?= htmlspecialchars(mb_strimwidth($row['content'], 0, 200, '…')) ?></p>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

    <footer><p>My Blog &copy; 2024</p></footer>
</body>
</html>
