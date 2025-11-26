<?php
session_start();

// Jika belum login, kembalikan ke login
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

include "../config.php";

// Pastikan ada ID di URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id']);

// Ambil data buku sebelum dihapus (untuk hapus cover)
$stmt = mysqli_prepare($conn, "SELECT cover FROM books WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$book = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$book) {
    echo "Data buku tidak ditemukan!";
    exit;
}

// Jika ada cover, hapus filenya
if (!empty($book['cover'])) {
    $path = __DIR__ . '/../uploads/' . $book['cover'];
    if (file_exists($path)) {
        unlink($path);
    }
}

// Hapus data buku dari database
$stmt = mysqli_prepare($conn, "DELETE FROM books WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// Kembali ke list data buku
header("Location: index.php");
exit;
?>
