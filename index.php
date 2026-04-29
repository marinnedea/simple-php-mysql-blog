<?php
require 'includes/config.php';
require 'includes/functions.php';

$categories = $db->query("SELECT id, name FROM categories ORDER BY name");
$posts = $db->query("
    SELECT p.id, p.title, p.content, p.date, p.featured_image,
           c.name AS category, c.id AS category_id
    FROM posts p
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.date DESC
");

require 'includes/header.php';
?>
    <div class="container">
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
                    <?php if ($row['featured_image']): ?>
                        <a href="view_post.php?id=<?= $row['id'] ?>">
                            <img class="featured-thumb" src="uploads/<?= htmlspecialchars($row['featured_image']) ?>"
                                 alt="<?= htmlspecialchars($row['title']) ?>">
                        </a>
                    <?php endif; ?>
                    <h2><a href="view_post.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?></a></h2>
                    <div class="meta">
                        <?= $row['date'] ?>
                        <?php if ($row['category']): ?>
                            &nbsp;·&nbsp;
                            <a href="category.php?id=<?= $row['category_id'] ?>"><?= htmlspecialchars($row['category']) ?></a>
                        <?php endif; ?>
                    </div>
                    <p><?= excerpt($row['content']) ?></p>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

<?php require 'includes/footer.php'; ?>
