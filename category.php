<?php
require 'includes/config.php';
require 'includes/functions.php';

if (!isset($_GET['id'])) { header('Location: index.php'); exit; }

$category_id = (int)$_GET['id'];

$cat_stmt = $db->prepare("SELECT name FROM categories WHERE id=?");
$cat_stmt->bind_param('i', $category_id);
$cat_stmt->execute();
$category = $cat_stmt->get_result()->fetch_assoc();
$cat_stmt->close();
if (!$category) { header('Location: index.php'); exit; }

$stmt = $db->prepare("
    SELECT p.id, p.title, p.content, p.date, p.featured_image
    FROM posts p
    WHERE p.category_id=?
    ORDER BY p.date DESC
");
$stmt->bind_param('i', $category_id);
$stmt->execute();
$result = $stmt->get_result();

$page_title = $category['name'];
require 'includes/header.php';
?>
    <div class="container">
        <h1><?= htmlspecialchars($category['name']) ?></h1>
        <?php if ($result->num_rows === 0): ?>
            <p>No posts in this category yet.</p>
        <?php else: ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="post-item">
                    <?php if ($row['featured_image']): ?>
                        <a href="view_post.php?id=<?= $row['id'] ?>">
                            <img class="featured-thumb" src="uploads/<?= htmlspecialchars($row['featured_image']) ?>"
                                 alt="<?= htmlspecialchars($row['title']) ?>">
                        </a>
                    <?php endif; ?>
                    <h2><a href="view_post.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?></a></h2>
                    <div class="meta"><?= $row['date'] ?></div>
                    <p><?= excerpt($row['content']) ?></p>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
        <a href="index.php">&larr; All Posts</a>
    </div>

<?php require 'includes/footer.php'; ?>
