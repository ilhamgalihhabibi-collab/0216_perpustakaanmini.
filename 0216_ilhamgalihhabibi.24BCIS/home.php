<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Perpustakaan Mini</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">Perpustakaan Mini</a>
        <div>
            <a href="books/index.php" class="btn btn-light btn-sm me-2">Kelola Buku</a>
            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </div>
</nav>

<!-- HERO SECTION -->
<div class="container mt-5">
    <div class="p-5 mb-4 bg-white shadow rounded-3">
        <div class="container-fluid py-5 text-center">
            <h1 class="display-5 fw-bold">Selamat Datang di Dashboard!</h1>
            <p class="col-md-10 mx-auto fs-5">
                Ini adalah Dashboard Perpustakaan Mini 0216 Anda.  
                Silakan kelola data buku melalui menu di bawah.
            </p>
            <a href="books/index.php" class="btn btn-primary btn-lg px-4 mt-3">Kelola Data Buku</a>
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer class="text-center py-3 text-muted">
    &copy; <?= date("Y"); ?> Perpustakaan Mini 0216 — Ujian Tengah Semester
</footer>

</body>
</html>
