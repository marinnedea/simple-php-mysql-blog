<?php
require 'includes/config.php';

$id = $_GET['id'];
$stmt = $db->prepare("SELECT * FROM posts WHERE id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($post['title']); ?></title>
</head>
<body>
    <h2><?php echo htmlspecialchars($post['title']); ?></h2>
    <p><?php echo htmlspecialchars($post['content']); ?></p>
    <p>Posted on: <?php echo $post['date']; ?></p>
    <a href="index.php">Back to Blog</a>
</body>
</html>
