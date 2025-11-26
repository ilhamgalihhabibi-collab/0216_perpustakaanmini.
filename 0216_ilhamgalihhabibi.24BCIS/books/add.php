<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

include "../config.php";

// Debug mode (opsional)
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tittle = trim($_POST['tittle'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = $_POST['category'] ?? 'Lainnya';
    $published_date = $_POST['published_date'] ?? null;

    // Upload cover
    $coverName = null;
    if (!empty($_FILES['cover']['name'])) {

        $ext = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
        $base = pathinfo($_FILES['cover']['name'], PATHINFO_FILENAME);
        $safeBase = preg_replace('/[^a-zA-Z0-9_-]/', '_', $base);

        $coverName = time() . '_' . $safeBase . '.' . $ext;
        $target = __DIR__ . '/../uploads/' . $coverName;

        if (!move_uploaded_file($_FILES['cover']['tmp_name'], $target)) {
            $errors[] = "Gagal mengupload cover.";
            $coverName = null;
        }
    }

    // Validasi
    if ($tittle === '' || $author === '') {
        $errors[] = "Judul dan author wajib diisi.";
    }

    if (empty($errors)) {

        // Ambil id user dari tabel users
        $created_by = null;
        if (!empty($_SESSION['username'])) {
            $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? LIMIT 1");
            mysqli_stmt_bind_param($stmt, "s", $_SESSION['username']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $uid);
            if (mysqli_stmt_fetch($stmt)) {
                $created_by = $uid;
            }
            mysqli_stmt_close($stmt);
        }

        // FIX: gunakan tabel BOOKS
        $stmt = mysqli_prepare($conn, 
            "INSERT INTO books (tittle, author, description, category, published_date, cover, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param($stmt, "ssssssi",
            $tittle, 
            $author, 
            $description, 
            $category, 
            $published_date, 
            $coverName, 
            $created_by
        );

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tambah Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h2>Tambah Buku</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach($errors as $e) echo "<div>" . htmlspecialchars($e) . "</div>"; ?>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">

        <div class="mb-3">
            <label>Judul</label>
            <input class="form-control" type="text" name="tittle" required>
        </div>

        <div class="mb-3">
            <label>Author</label>
            <input class="form-control" type="text" name="author" required>
        </div>

        <div class="mb-3">
            <label>Deskripsi</label>
            <textarea class="form-control" name="description" rows="4"></textarea>
        </div>

        <div class="mb-3">
            <label>Kategori</label><br>
            <?php
            $options = ['Fiksi','Non-Fiksi','Referensi','Lainnya'];
            foreach ($options as $opt) {
                echo '<label class="me-3">
                        <input type="radio" name="category" value="'.$opt.'"> '.$opt.'
                      </label>';
            }
            ?>
        </div>

        <div class="mb-3">
            <label>Tanggal Terbit</label>
            <input class="form-control" type="date" name="published_date">
        </div>

        <div class="mb-3">
            <label>Cover (file)</label>
            <input class="form-control" type="file" name="cover">
        </div>

        <button class="btn btn-primary" type="submit">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
</body>
</html>
