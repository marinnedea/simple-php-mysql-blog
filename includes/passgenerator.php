<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Password Generator</title>
</head>
<body>
    <h1>Admin Password Generator</h1>
    <form method="post" action="">
        <label for="password">Enter Password:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Generate Hash</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $password = $_POST["password"];
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        echo "<p>Hashed Password: <strong>" . htmlspecialchars($hashed_password) . "</strong></p>";
    }
    ?>
</body>
</html>
