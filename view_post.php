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
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div id="branding">
                <h1>My Blog</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <div class="container">
        <h2><?php echo htmlspecialchars($post['title']); ?></h2>
        <p><?php echo htmlspecialchars($post['content']); ?></p>
        <p>Posted on: <?php echo $post['date']; ?></p>
        <a href="index.php">Back to Blog</a>
    </div>
    <footer>
        <p>My Blog &copy; 2024</p>
    </footer>
</body>
</html>
