<?php
require 'includes/config.php';

if (!isset($_GET['id'])) { header('Location: index.php'); exit; }

$id = (int)$_GET['id'];
$stmt = $db->prepare("
    SELECT p.title, p.content, p.date, p.featured_image,
           c.name AS category, c.id AS category_id
    FROM posts p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.id=?
");
$stmt->bind_param('i', $id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$post) { header('Location: index.php'); exit; }

$page_title = $post['title'];
require 'includes/header.php';
?>
    <div class="container">
        <a href="index.php" class="btn-back">&larr; Back to Blog</a>
        <?php if ($post['featured_image']): ?>
            <img class="featured-hero" src="uploads/<?= htmlspecialchars($post['featured_image']) ?>"
                 alt="<?= htmlspecialchars($post['title']) ?>">
        <?php endif; ?>

        <h1><?= htmlspecialchars($post['title']) ?></h1>
        <div class="meta">
            <?= $post['date'] ?>
            <?php if ($post['category']): ?>
                &nbsp;·&nbsp;
                <a href="category.php?id=<?= $post['category_id'] ?>"><?= htmlspecialchars($post['category']) ?></a>
            <?php endif; ?>
        </div>

        <div class="post-content">
            <?= $post['content'] ?>
        </div>

    </div>

<?php require 'includes/footer.php'; ?>
