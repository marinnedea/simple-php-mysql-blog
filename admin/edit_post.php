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
                </ul>
            </nav>
        </div>
    </header>
    <div class="container">
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
    </div>
    <footer>
        <p>Admin Panel &copy; 2024</p>
    </footer>
</body>
</html>
