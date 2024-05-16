<?php
session_start();
require '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['token'], $_POST['token'])) {
        die('Invalid CSRF token');
    }

    $title = $_POST['title'];
    $content = $_POST['content'];
    $category_id = $_POST['category_id'];

    $stmt = $db->prepare("INSERT INTO posts (title, content, date, category_id) VALUES (?, ?, NOW(), ?)");
    $stmt->bind_param('ssi', $title, $content, $category_id);

    if ($stmt->execute()) {
        echo "New post created successfully";
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
    <title>Add Post</title>
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
                </ul>
            </nav>
        </div>
    </header>
    <div class="container">
        <form method="post">
            <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
            Title: <input type="text" name="title"><br>
            Content: <textarea name="content"></textarea><br>
            Category:
            <select name="category_id">
                <?php
                $categories = $db->query("SELECT * FROM categories");
                while ($cat = $categories->fetch_assoc()):
                ?>
                    <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                <?php endwhile; ?>
            </select><br>
            <input type="submit" value="Add Post">
        </form>
        <a href="admin.php">Back to Admin Panel</a>
    </div>
    <footer>
        <p>Admin Panel &copy; 2024</p>
    </footer>
</body>
</html>
