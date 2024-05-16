<?php
require 'includes/config.php';

$categories = $db->query("SELECT * FROM categories");
$posts = $db->query("SELECT * FROM posts ORDER BY date DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Blog</title>
</head>
<body>
    <h1>Blog Posts</h1>
    <h2>Categories</h2>
    <?php while ($cat = $categories->fetch_assoc()): ?>
        <a href="category.php?id=<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></a><br>
    <?php endwhile; ?>
    <h2>All Posts</h2>
    <?php while ($row = $posts->fetch_assoc()): ?>
        <h2><a href="category/<?php echo urlencode($row['category_id']); ?>/article/<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['title']); ?></a></h2>
        <p><?php echo htmlspecialchars($row['content']); ?></p>
        <p>Posted on: <?php echo $row['date']; ?></p>
    <?php endwhile; ?>
</body>
</html>
