<?php
// config.php
// Sesuaikan username/password MySQL jika perlu
$host = "localhost";
$user = "root";
$pass = "";
$db   = "database_0216";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Optional: aktifkan error display saat debugging (hapus/comment di production)
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
?>
