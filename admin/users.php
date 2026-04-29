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

    if ($action === 'add') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $message = "Username and password are required.";
        } else {
            $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bind_param('ss', $username, $hash);
            if ($stmt->execute()) {
                $message = "User '{$username}' created.";
            } else {
                $message = "Username already exists.";
            }
            $stmt->close();
        }

    } elseif ($action === 'change_password') {
        $id = (int)($_POST['id'] ?? 0);
        $password = $_POST['password'] ?? '';

        if ($id && $password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET password=? WHERE id=?");
            $stmt->bind_param('si', $hash, $id);
            $stmt->execute();
            $stmt->close();
            $message = "Password updated.";
        }

    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);

        // Fetch current user's id to prevent self-deletion
        $self = $db->prepare("SELECT id FROM users WHERE username=?");
        $self->bind_param('s', $_SESSION['username']);
        $self->execute();
        $self_id = (int)($self->get_result()->fetch_assoc()['id'] ?? 0);
        $self->close();

        if ($id && $id !== $self_id) {
            $stmt = $db->prepare("DELETE FROM users WHERE id=?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
            $message = "User deleted.";
        } else {
            $message = "You cannot delete your own account.";
        }
    }
}

$_SESSION['token'] = bin2hex(random_bytes(32));
$users = $db->query("SELECT id, username FROM users ORDER BY username");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
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
        <h1>Users</h1>

        <?php if ($message): ?>
            <p><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <h2>Add User</h2>
        <form method="post">
            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
            <input type="hidden" name="action" value="add">
            Username: <input type="text" name="username" required><br>
            Password: <input type="password" name="password" required><br>
            <input type="submit" value="Create User">
        </form>

        <h2>Existing Users</h2>
        <?php if ($users->num_rows === 0): ?>
            <p>No users found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Change Password</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['username']) ?>
                                <?php if ($user['username'] === $_SESSION['username']): ?>
                                    <em>(you)</em>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="post" style="display:inline">
                                    <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
                                    <input type="hidden" name="action" value="change_password">
                                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                    <input type="password" name="password" placeholder="New password" required>
                                    <button type="submit">Update</button>
                                </form>
                            </td>
                            <td>
                                <?php if ($user['username'] !== $_SESSION['username']): ?>
                                    <form method="post" style="display:inline"
                                          onsubmit="return confirm('Delete user <?= htmlspecialchars($user['username'], ENT_QUOTES) ?>?')">
                                        <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                        <button type="submit" class="btn-danger">Delete</button>
                                    </form>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
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
