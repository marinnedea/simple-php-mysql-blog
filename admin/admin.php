<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
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
                    <li><a href="categories.php">Categories</a></li>
                    <li><a href="users.php">Users</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <div class="container">
        <h1>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></h1>
        <ul>
            <li><a href="posts.php">Manage Posts</a></li>
            <li><a href="add_post.php">Add New Post</a></li>
            <li><a href="categories.php">Manage Categories</a></li>
            <li><a href="users.php">Manage Users</a></li>
            <li><a href="../index.php">View Blog</a></li>
        </ul>
    </div>
    <footer>
        <p>Admin Panel &copy; 2024</p>
    </footer>
</body>
</html>
