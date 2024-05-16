<?php
require 'includes/config.php';

$category_id = $_GET['id'];
$stmt = $db->prepare("SELECT * FROM posts WHERE category_id=? ORDER BY date DESC");
$stmt->bind_param('i', $category_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Category</title>
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
        <h1>Posts in Category</h1>
        <?php while ($row = $result->fetch_assoc()): ?>
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
