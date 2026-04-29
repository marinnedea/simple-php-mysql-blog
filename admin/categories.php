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

    $action = $_POST['action'] ?? '';

    if ($action === 'add' && !empty(trim($_POST['name'] ?? ''))) {
        $stmt = $db->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param('s', $_POST['name']);
        $stmt->execute();
        $stmt->close();
        $message = "Category added.";

    } elseif ($action === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id && !empty(trim($_POST['name'] ?? ''))) {
            $stmt = $db->prepare("UPDATE categories SET name=? WHERE id=?");
            $stmt->bind_param('si', $_POST['name'], $id);
            $stmt->execute();
            $stmt->close();
            $message = "Category updated.";
        }

    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $stmt = $db->prepare("DELETE FROM categories WHERE id=?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
            $message = "Category deleted. Posts in this category are now uncategorised.";
        }
    }
}

$_SESSION['token'] = bin2hex(random_bytes(32));
$categories = $db->query("
    SELECT c.id, c.name, COUNT(p.id) AS post_count
    FROM categories c
    LEFT JOIN posts p ON p.category_id = c.id
    GROUP BY c.id
    ORDER BY c.name
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Categories</title>
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
                    <li><a href="add_post.php">Add Post</a></li>
                    <li><a href="users.php">Users</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <div class="container">
        <h1>Categories</h1>
        <?php if ($message): ?>
            <p><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <h2>Add Category</h2>
        <form method="post">
            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
            <input type="hidden" name="action" value="add">
            <input type="text" name="name" placeholder="Category name" required>
            <input type="submit" value="Add">
        </form>

        <h2>Existing Categories</h2>
        <?php if ($categories->num_rows === 0): ?>
            <p>No categories yet.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Posts</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($cat = $categories->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <form method="post" style="display:inline">
                                    <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
                                    <input type="hidden" name="action" value="edit">
                                    <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                    <input type="text" name="name"
                                           value="<?= htmlspecialchars($cat['name']) ?>" required>
                                    <button type="submit">Save</button>
                                </form>
                            </td>
                            <td><?= $cat['post_count'] ?></td>
                            <td>
                                <form method="post" style="display:inline"
                                      onsubmit="return confirm('Delete this category? Posts will become uncategorised.')">
                                    <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                    <button type="submit" class="btn-danger">Delete</button>
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
