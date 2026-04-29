<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
require '../includes/config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['token']) || !hash_equals($_SESSION['token'], $_POST['token'] ?? '')) {
        die('Invalid CSRF token.');
    }

    $id = (int)($_POST['id'] ?? 0);
    if ($id) {
        $stmt = $db->prepare("DELETE FROM posts WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
        $message = "Post deleted.";
    }
}

$_SESSION['token'] = bin2hex(random_bytes(32));

$posts = $db->query("
    SELECT p.id, p.title, p.date, c.name AS category
    FROM posts p
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.date DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Posts</title>
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
                    <li><a href="add_post.php">Add Post</a></li>
                    <li><a href="categories.php">Categories</a></li>
                    <li><a href="users.php">Users</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <div class="container">
        <h1>Posts</h1>
        <?php if ($message): ?>
            <p><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <?php if ($posts->num_rows === 0): ?>
            <p>No posts yet. <a href="add_post.php">Add one</a>.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $posts->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= htmlspecialchars($row['category'] ?? '—') ?></td>
                            <td><?= $row['date'] ?></td>
                            <td>
                                <a href="edit_post.php?id=<?= $row['id'] ?>">Edit</a>
                                &nbsp;|&nbsp;
                                <form method="post" style="display:inline"
                                      onsubmit="return confirm('Delete this post?')">
                                    <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <button type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <footer>
        <p>Admin Panel &copy; 2024</p>
    </footer>
</body>
</html>
