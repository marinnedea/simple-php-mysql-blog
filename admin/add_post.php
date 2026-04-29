<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
require '../includes/config.php';
require '../includes/functions.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['token']) || !hash_equals($_SESSION['token'], $_POST['token'] ?? '')) {
        die('Invalid CSRF token.');
    }

    $featured_image = null;
    if (!empty($_FILES['featured_image']['name'])) {
        $featured_image = save_featured_image($_FILES['featured_image'], dirname(__DIR__) . '/uploads/');
        if (!$featured_image) $message = "Image upload failed — post saved without featured image.";
    }

    $content = sanitize_html($_POST['content'] ?? '');
    $stmt = $db->prepare("INSERT INTO posts (title, content, date, category_id, featured_image) VALUES (?, ?, NOW(), ?, ?)");
    $stmt->bind_param('ssis', $_POST['title'], $content, $_POST['category_id'], $featured_image);

    if ($stmt->execute()) {
        $message = $message ?: "Post created successfully.";
    } else {
        error_log('Database error: ' . $stmt->error);
        $message = "An error occurred. Please try again.";
    }
    $stmt->close();
}

$_SESSION['token'] = bin2hex(random_bytes(32));
$categories = $db->query("SELECT id, name FROM categories");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Post</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.tiny.cloud/1/<?= TINYMCE_API_KEY ?>/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
    tinymce.init({
        selector: '#content',
        plugins: 'link image lists table code',
        toolbar: 'undo redo | blocks | bold italic underline | bullist numlist | link image | code',
        menubar: false,
        resize: true,
        min_height: 320,
        images_upload_url: 'upload_image.php',
        automatic_uploads: true,
        file_picker_types: 'image',
        content_css: '../css/style.css',
        body_class: 'tinymce-body',
    });
    </script>
</head>
<body>
    <header>
        <div class="container">
            <div id="branding"><h1>Admin Panel</h1></div>
            <nav>
                <ul>
                    <li><a href="admin.php">Home</a></li>
                    <li><a href="posts.php">Posts</a></li>
                    <li><a href="categories.php">Categories</a></li>
                    <li><a href="users.php">Users</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <div class="container">
        <h1>Add Post</h1>
        <?php if ($message): ?>
            <p><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">

            <label for="title">Title</label>
            <input type="text" id="title" name="title" required>

            <label for="category_id">Category</label>
            <select id="category_id" name="category_id">
                <?php while ($cat = $categories->fetch_assoc()): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endwhile; ?>
            </select>

            <label for="featured_image">Featured Image <small>(JPEG, PNG, GIF, WebP — max 5 MB)</small></label>
            <input type="file" id="featured_image" name="featured_image" accept="image/*">

            <label for="content">Content</label>
            <textarea id="content" name="content"></textarea>

            <input type="submit" value="Publish Post">
        </form>
        <a href="posts.php">Back to Posts</a>
    </div>
    <footer><p>Admin Panel &copy; 2024</p></footer>
</body>
</html>
