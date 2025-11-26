<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

include "../config.php";

// ==============================
// SEARCH & FILTER HANDLING
// ==============================
$where = "WHERE 1";

// search judul / author
if (!empty($_GET['q'])) {
    $q = "%" . mysqli_real_escape_string($conn, $_GET['q']) . "%";
    $where .= " AND (tittle LIKE '$q' OR author LIKE '$q')";
}

// filter kategori
if (!empty($_GET['kategori'])) {
    $kategori = mysqli_real_escape_string($conn, $_GET['kategori']);
    $where .= " AND category = '$kategori'";
}

// final query
$query = "SELECT * FROM books $where ORDER BY id DESC";
$books = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
<div class="container mt-4">

    <h2 class="mb-4">Data Buku</h2>

    <a href="add.php" class="btn btn-primary mb-3">+ Tambah Buku</a>
    <a href="../home.php" class="btn btn-secondary mb-3">Kembali ke Dashboard</a>

    <!-- ==============================
         FORM PENCARIAN + FILTER
    =============================== -->
    <form method="get" class="mb-3 d-flex gap-2">

        <!-- search -->
        <input type="text" name="q" class="form-control"
               placeholder="Cari judul / author..."
               value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">

        <!-- filter kategori -->
        <select name="kategori" class="form-select" style="max-width:200px;">
            <option value="">Semua Kategori</option>
            <option value="Fiksi"        <?= (($_GET['kategori'] ?? '')=='Fiksi')?'selected':'' ?>>Fiksi</option>
            <option value="Non-Fiksi"    <?= (($_GET['kategori'] ?? '')=='Non-Fiksi')?'selected':'' ?>>Non-Fiksi</option>
            <option value="Referensi"    <?= (($_GET['kategori'] ?? '')=='Referensi')?'selected':'' ?>>Referensi</option>
            <option value="Lainnya"      <?= (($_GET['kategori'] ?? '')=='Lainnya')?'selected':'' ?>>Lainnya</option>
        </select>

        <button class="btn btn-primary">Cari</button>
    </form>

    <!-- ==============================
         TABEL DATA BUKU
    =============================== -->
    <table class="table table-bordered table-striped bg-white shadow-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Judul</th>
                <th>Author</th>
                <th>Deskripsi</th>
                <th>Kategori</th>
                <th>Tanggal Terbit</th>
                <th>Cover</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            <?php while ($row = mysqli_fetch_assoc($books)) : ?>
            <tr>
                <td><?= htmlspecialchars($row['id']); ?></td>
                <td><?= htmlspecialchars($row['tittle']); ?></td>
                <td><?= htmlspecialchars($row['author']); ?></td>
                <td style="max-width:300px;"><?= nl2br(htmlspecialchars($row['description'])); ?></td>
                <td><?= htmlspecialchars($row['category']); ?></td>
                <td><?= htmlspecialchars($row['published_date']); ?></td>
                <td>
                    <?php if (!empty($row['cover'])) : ?>
                        <a href="../uploads/<?= urlencode($row['cover']); ?>" target="_blank">Lihat</a>
                    <?php else : ?>
                        Tidak ada
                    <?php endif; ?>
                </td>
                <td>
                    <a href="edit.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="delete.php?id=<?= $row['id']; ?>" 
                       class="btn btn-danger btn-sm" 
                       onclick="return confirm('Yakin hapus?')">
                        Hapus
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>

    </table>
</div>
</body>
</html>
