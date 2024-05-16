<?php
require 'includes/config.php';

$categories = $db->query("SELECT * FROM categories");
$posts = $db->query("SELECT * FROM posts ORDER BY date DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Blog</title>
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
        <h1>Blog Posts</h1>
        <h2>Categories</h2>
        <?php while ($cat = $categories->fetch_assoc()): ?>
            <a href="category.php?id=<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></a><br>
        <?php endwhile; ?>
        <h2>All Posts</h2>
        <?php while ($row = $posts->fetch_assoc()): ?>
            <h2><a href="view_post.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['title']); ?></a></h2>
            <p><?php echo htmlspecialchars($row['content']); ?></p>
            <p>Posted on: <?php echo $row['date']; ?></p>
        <?php endwhile; ?>
    </div>
    <footer>
        <p>My Blog &copy; 2024</p>
    </footer>
</body>
</html>
