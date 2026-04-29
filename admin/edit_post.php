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

    $id = (int)($_POST['id'] ?? 0);
    $existing_image = $_POST['existing_image'] ?? null;
    $featured_image = $existing_image;

    // Remove image if checkbox ticked
    if (isset($_POST['remove_image']) && $existing_image) {
        @unlink(dirname(__DIR__) . '/uploads/' . $existing_image);
        $featured_image = null;
    }

    // Replace image if a new one was uploaded
    if (!empty($_FILES['featured_image']['name'])) {
        $new_image = save_featured_image($_FILES['featured_image'], dirname(__DIR__) . '/uploads/');
        if ($new_image) {
            if ($existing_image) @unlink(dirname(__DIR__) . '/uploads/' . $existing_image);
            $featured_image = $new_image;
        } else {
            $message = "Image upload failed — keeping existing image.";
        }
    }

    $content = sanitize_html($_POST['content'] ?? '');
    $stmt = $db->prepare("UPDATE posts SET title=?, content=?, category_id=?, featured_image=? WHERE id=?");
    $stmt->bind_param('ssisi', $_POST['title'], $content, $_POST['category_id'], $featured_image, $id);

    if ($stmt->execute()) {
        $message = $message ?: "Post updated successfully.";
    } else {
        error_log('Database error: ' . $stmt->error);
        $message = "An error occurred. Please try again.";
    }
    $stmt->close();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : (int)($_POST['id'] ?? 0);
if (!$id) { header('Location: posts.php'); exit; }

$stmt = $db->prepare("SELECT id, title, content, category_id, featured_image FROM posts WHERE id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$post) { header('Location: posts.php'); exit; }

$_SESSION['token'] = bin2hex(random_bytes(32));
$categories = $db->query("SELECT id, name FROM categories");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Post</title>
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
        <h1>Edit Post</h1>
        <?php if ($message): ?>
            <p><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
            <input type="hidden" name="id" value="<?= $post['id'] ?>">
            <input type="hidden" name="existing_image" value="<?= htmlspecialchars($post['featured_image'] ?? '') ?>">

            <label for="title">Title</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>

            <label for="category_id">Category</label>
            <select id="category_id" name="category_id">
                <?php while ($cat = $categories->fetch_assoc()): ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $post['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Featured Image</label>
            <?php if ($post['featured_image']): ?>
                <div class="featured-image-preview">
                    <img src="../uploads/<?= htmlspecialchars($post['featured_image']) ?>"
                         alt="Current featured image">
                    <label>
                        <input type="checkbox" name="remove_image" value="1">
                        Remove current image
                    </label>
                </div>
            <?php endif; ?>
            <input type="file" id="featured_image" name="featured_image" accept="image/*">
            <small>Upload a new image to replace the current one. JPEG, PNG, GIF, WebP — max 5 MB.</small>

            <label for="content">Content</label>
            <textarea id="content" name="content"><?= htmlspecialchars($post['content']) ?></textarea>

            <input type="submit" value="Save Changes">
        </form>
        <a href="posts.php">Back to Posts</a>
    </div>
    <footer><p>Admin Panel &copy; 2024</p></footer>
</body>
</html>
