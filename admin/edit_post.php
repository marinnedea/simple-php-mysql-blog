<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
require '../includes/config.php';

$message = '';

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['token']) || !hash_equals($_SESSION['token'], $_POST['token'] ?? '')) {
        die('Invalid CSRF token.');
    }

    $stmt = $db->prepare("UPDATE posts SET title=?, content=?, category_id=? WHERE id=?");
    $stmt->bind_param('ssii', $_POST['title'], $_POST['content'], $_POST['category_id'], $_POST['id']);

    if ($stmt->execute()) {
        $message = "Post updated successfully.";
    } else {
        error_log('Database error: ' . $stmt->error);
        $message = "An error occurred. Please try again.";
    }
    $stmt->close();
}

// Fetch post
$id = isset($_GET['id']) ? (int)$_GET['id'] : (int)($_POST['id'] ?? 0);
if (!$id) {
    header('Location: admin.php');
    exit;
}

$stmt = $db->prepare("SELECT id, title, content, category_id FROM posts WHERE id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$post) {
    header('Location: admin.php');
    exit;
}

$_SESSION['token'] = bin2hex(random_bytes(32));
$categories = $db->query("SELECT id, name FROM categories");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Post</title>
    <link rel="stylesheet" type="text/css" href="../css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div id="branding">
                <h1>Admin Panel</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="admin.php">Home</a></li>
                    <li><a href="posts.php">Posts</a></li>
                    <li><a href="categories.php">Categories</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <div class="container">
        <?php if ($message): ?>
            <p><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <form method="post">
            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
            <input type="hidden" name="id" value="<?= $post['id'] ?>">
            Title: <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required><br>
            Content: <textarea name="content" required><?= htmlspecialchars($post['content']) ?></textarea><br>
            Category:
            <select name="category_id">
                <?php while ($cat = $categories->fetch_assoc()): ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $post['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select><br>
            <input type="submit" value="Save Changes">
        </form>
        <a href="admin.php">Back to Admin Panel</a>
    </div>
    <footer>
        <p>Admin Panel &copy; 2024</p>
    </footer>
</body>
</html>
