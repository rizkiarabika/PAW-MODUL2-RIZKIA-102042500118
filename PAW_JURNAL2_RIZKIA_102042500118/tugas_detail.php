
<?php
require_once 'koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $conn->prepare(
    "SELECT tugas.*, mata_kuliah.nama AS nama_mk
     FROM tugas
     JOIN mata_kuliah ON tugas.mata_kuliah_id = mata_kuliah.id
     WHERE tugas.id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result();
$tugas = $data->fetch_assoc();

if (!$tugas) {
    die("Tugas tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Tugas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="app-layout">

    <aside class="sidebar">
        <h2 class="logo">LMS</h2>
        <p class="logo-description">Learning Management System</p>

        <nav class="nav-menu">
            <a href="index.php">Dashboard</a>
            <a href="matakuliah_index.php">Mata Kuliah</a>
            <a href="materi_index.php">Materi</a>
            <a href="tugas_index.php" class="active">Tugas</a>
        </nav>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Detail Tugas</h1>

        <section class="content-card">
            <p><strong>Mata Kuliah:</strong>
                <?= htmlspecialchars($tugas['nama_mk']) ?></p>

            <p><strong>Judul Tugas:</strong>
                <?= htmlspecialchars($tugas['judul']) ?></p>

            <p><strong>Instruksi:</strong></p>
            <p><?= nl2br(htmlspecialchars($tugas['instruksi'])) ?></p>

            <p><strong>Tenggat:</strong>
                <?= htmlspecialchars($tugas['tenggat']) ?></p>

            <p><strong>Status:</strong>
                <?= htmlspecialchars($tugas['status']) ?></p>

            <a href="tugas_index.php" class="btn btn-secondary">
                Kembali
            </a>
        </section>
    </main>

</div>
</body>
</html>
