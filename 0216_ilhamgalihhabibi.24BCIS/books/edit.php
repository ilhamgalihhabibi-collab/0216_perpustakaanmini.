<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

include "../config.php";

// CEK apakah ada id
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id']);

// Ambil data buku yang mau diedit
$stmt = mysqli_prepare($conn, "SELECT * FROM books WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$book = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$book) {
    echo "Data buku tidak ditemukan!";
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tittle = trim($_POST['tittle'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = $_POST['category'] ?? 'Lainnya';
    $published_date = $_POST['published_date'] ?? null;

    // COVER: tetap pakai cover lama kecuali upload baru
    $coverName = $book['cover'];

    if (!empty($_FILES['cover']['name'])) {
        // sama seperti add.php
        $ext = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
        $base = pathinfo($_FILES['cover']['name'], PATHINFO_FILENAME);
        $safeBase = preg_replace('/[^a-zA-Z0-9_-]/', '_', $base);

        $newCover = time() . '_' . $safeBase . '.' . $ext;
        $target = __DIR__ . '/../uploads/' . $newCover;

        if (move_uploaded_file($_FILES['cover']['tmp_name'], $target)) {

            // Hapus file lama kalau ada
            if (!empty($book['cover']) && file_exists(__DIR__ . '/../uploads/' . $book['cover'])) {
                unlink(__DIR__ . '/../uploads/' . $book['cover']);
            }

            $coverName = $newCover;

        } else {
            $errors[] = "Gagal mengupload cover baru.";
        }
    }

    // Validasi
    if ($tittle === '' || $author === '') {
        $errors[] = "Judul dan Author wajib diisi.";
    }

    if (empty($errors)) {
        
        $stmt = mysqli_prepare($conn,
            "UPDATE books SET tittle = ?, author = ?, description = ?, category = ?, published_date = ?, cover = ? 
             WHERE id = ?"
        );

        mysqli_stmt_bind_param($stmt, "ssssssi",
            $tittle,
            $author,
            $description,
            $category,
            $published_date,
            $coverName,
            $id
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
    <title>Edit Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">

    <h2>Edit Buku</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $e) echo "<div>" . htmlspecialchars($e) . "</div>"; ?>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">

        <div class="mb-3">
            <label>Judul</label>
            <input class="form-control" type="text" name="tittle" value="<?= htmlspecialchars($book['tittle']); ?>" required>
        </div>

        <div class="mb-3">
            <label>Author</label>
            <input class="form-control" type="text" name="author" value="<?= htmlspecialchars($book['author']); ?>" required>
        </div>

        <div class="mb-3">
            <label>Deskripsi</label>
            <textarea class="form-control" name="description" rows="4"><?= htmlspecialchars($book['description']); ?></textarea>
        </div>

        <div class="mb-3">
            <label>Kategori</label><br>
            <?php
            $options = ['Fiksi','Non-Fiksi','Referensi','Lainnya'];
            foreach ($options as $opt) {
                $checked = ($book['category'] === $opt) ? "checked" : "";
                echo '<label class="me-3">
                        <input type="radio" name="category" value="'.$opt.'" '.$checked.'> '.$opt.'
                      </label>';
            }
            ?>
        </div>

        <div class="mb-3">
            <label>Tanggal Terbit</label>
            <input class="form-control" type="date" name="published_date" value="<?= htmlspecialchars($book['published_date']); ?>">
        </div>

        <div class="mb-3">
            <label>Cover Lama:</label><br>
            <?php if (!empty($book['cover'])): ?>
                <img src="../uploads/<?= $book['cover']; ?>" width="120" class="mb-2">
            <?php else: ?>
                <div>Tidak ada cover</div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label>Upload Cover Baru (opsional)</label>
            <input class="form-control" type="file" name="cover">
        </div>

        <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>

    </form>
</div>
</body>
</html>
