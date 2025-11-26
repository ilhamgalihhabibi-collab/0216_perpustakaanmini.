<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <h2>Dashboard Perpustakaan Mini</h2>

    <a href="books/index.php">Kelola Buku</a> | 
    <a href="logout.php">Logout</a>

</body>
</html>
