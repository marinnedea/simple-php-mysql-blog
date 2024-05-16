<?php
session_start();
require '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['token'], $_POST['token'])) {
        die('Invalid CSRF token');
    }

    $id = $_POST['id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category_id = $_POST['category_id'];

    $stmt = $db->prepare("UPDATE posts SET title=?, content=?, date=NOW(), category_id=? WHERE id=?");
    $stmt->bind_param('ssii', $title, $content, $category_id, $id);

    if ($stmt->execute()) {
        echo "Post updated successfully";
    } else {
        error_log('Database error: ' . $stmt->error);
        die('An error occurred. Please try again later.');
    }

    $stmt->close();
}
$_SESSION['token'] = bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Post</title>
</head>
<body>
    <form method="post">
        <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
        <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
        Title: <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>"><br>
        Content: <textarea name="content"><?php echo htmlspecialchars($post['content']); ?></textarea><br>
        Category:
        <select name="category_id">
            <?php
            $categories = $db->query("SELECT * FROM categories");
            while ($cat = $categories->fetch_assoc()):
            ?>
                <option value="<?php echo $cat['id']; ?>" <?php echo ($cat['id'] == $post['category_id']) ? 'selected' : ''; ?>>
                    <?php echo $cat['name']; ?>
                </option>
            <?php endwhile; ?>
        </select><br>
        <input type="submit" value="Edit Post">
    </form>
    <a href="admin.php">Back to Admin Panel</a>
</body>
</html>
