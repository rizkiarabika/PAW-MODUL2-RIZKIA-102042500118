
<?php
require_once 'koneksi.php';

$id = (int) ($_GET['id'] ?? 0);
$stmt = $conn->prepare(
    "SELECT materi.*, mata_kuliah.nama AS nama_mk
     FROM materi JOIN mata_kuliah
     ON materi.mata_kuliah_id = mata_kuliah.id
     WHERE materi.id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) die("Materi tidak ditemukan.");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Materi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="main-content">
    <h1 class="page-title">Detail Materi</h1>
    <section class="content-card">
        <p><b>Mata Kuliah:</b> <?= htmlspecialchars($data['nama_mk']) ?></p><br>
        <p><b>Judul:</b> <?= htmlspecialchars($data['judul']) ?></p><br>
        <p><b>Isi Materi:</b><br><?= nl2br(htmlspecialchars($data['isi'])) ?></p><br>

        <?php if ($data['tautan']): ?>
            <p><b>Tautan:</b>
                <a href="<?= htmlspecialchars($data['tautan']) ?>" target="_blank">
                    Buka Materi
                </a>
            </p><br>
        <?php endif; ?>

        <a href="materi_index.php" class="btn btn-secondary">Kembali</a>
    </section>
</main>
</body>
</html>
