<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
} else {
    header('Location: admin.php');
    exit;
}
?>