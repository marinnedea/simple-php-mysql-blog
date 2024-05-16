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
</head>
<body>
    <h1>Posts in Category</h1>
    <?php while ($row = $result->fetch_assoc()): ?>
        <h2><a href="view_post.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['title']); ?></a></h2>
        <p><?php echo htmlspecialchars($row['content']); ?></p>
        <p>Posted on: <?php echo $row['date']; ?></p>
    <?php endwhile; ?>
</body>
</html>
