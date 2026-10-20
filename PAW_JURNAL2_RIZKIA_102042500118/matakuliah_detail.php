
<?php
require_once 'koneksi.php';

$id = (int) ($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM mata_kuliah WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die("Data mata kuliah tidak ditemukan.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Mata Kuliah</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="main-content">
    <h1 class="page-title">Detail Mata Kuliah</h1>

    <section class="content-card">
        <p><b>Kode:</b> <?= htmlspecialchars($data['kode']) ?></p><br>
        <p><b>Nama:</b> <?= htmlspecialchars($data['nama']) ?></p><br>
        <p><b>Dosen:</b> <?= htmlspecialchars($data['dosen']) ?></p><br>
        <p><b>Semester:</b> <?= $data['semester'] ?></p><br>

        <a href="matakuliah_index.php" class="btn btn-secondary">Kembali</a>
    </section>
</main>
</body>
</html>
