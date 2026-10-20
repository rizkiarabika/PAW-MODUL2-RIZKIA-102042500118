
<?php
require_once 'koneksi.php';

$mk = '';
$judul = '';
$instruksi = '';
$tenggat = '';
$status = '';
$pesan = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mk = trim($_POST['mata_kuliah_id'] ?? '');
    $judul = trim($_POST['judul'] ?? '');
    $instruksi = trim($_POST['instruksi'] ?? '');
    $tenggat = trim($_POST['tenggat'] ?? '');
    $status = trim($_POST['status'] ?? '');

    if (
        $mk === '' || !ctype_digit($mk) || (int)$mk < 1 ||
        $judul === '' || $instruksi === '' || $tenggat === ''
    ) {
        $pesan = "Semua data wajib diisi dengan benar.";
    } elseif (!in_array($status, ['Draf', 'Aktif', 'Selesai'], true)) {
        $pesan = "Status harus Draf, Aktif, atau Selesai.";
    } else {
        $tanggal = DateTime::createFromFormat('!Y-m-d', $tenggat);

        if (!$tanggal || $tanggal->format('Y-m-d') !== $tenggat) {
            $pesan = "Tanggal tenggat tidak valid. Gunakan format YYYY-MM-DD.";
        } else {
            $id_mk = (int)$mk;

            $cek = $conn->prepare(
                "SELECT id FROM mata_kuliah WHERE id = ?"
            );
            $cek->bind_param("i", $id_mk);
            $cek->execute();
            $hasil = $cek->get_result();

            if ($hasil->num_rows == 0) {
                $pesan = "Mata kuliah tidak ditemukan.";
            } else {
                $stmt = $conn->prepare(
                    "INSERT INTO tugas
                    (mata_kuliah_id, judul, instruksi, tenggat, status)
                    VALUES (?, ?, ?, ?, ?)"
                );

                $stmt->bind_param(
                    "issss",
                    $id_mk,
                    $judul,
                    $instruksi,
                    $tenggat,
                    $status
                );

                if ($stmt->execute()) {
                    header("Location: tugas_index.php");
                    exit;
                }

                $pesan = "Tugas gagal ditambahkan.";
            }
        }
    }
}

$mata_kuliah = $conn->query(
    "SELECT id, nama FROM mata_kuliah ORDER BY nama ASC"
);

function h($nilai) {
    return htmlspecialchars((string)$nilai, ENT_QUOTES, 'UTF-8');
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Tugas</title>
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
        <h1 class="page-title">Tambah Tugas</h1>

        <?php if (isset($pesan)): ?>
            <p class="alert"><?= htmlspecialchars($pesan) ?></p>
        <?php endif; ?>

        <section class="content-card">
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">ID Mata Kuliah</label>
                    <input type="text" name="mata_kuliah_id"
                        class="form-control"
                        value="<?= h($mk) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Judul Tugas</label>
                    <input type="text" name="judul"
                           class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Instruksi</label>
                    <textarea name="instruksi"
                              class="form-control" required></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Tenggat (YYYY-MM-DD)</label>
                    <input type="text" name="tenggat"
                        class="form-control"
                        placeholder="Contoh: 2026-12-30"
                        value="<?= h($tenggat) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <input type="text" name="status"
                        class="form-control"
                        placeholder="Draf, Aktif, atau Selesai"
                        value="<?= h($status) ?>" required>
                </div>

                <button type="submit" class="btn">Simpan Tugas</button>
                <a href="tugas_index.php" class="btn btn-secondary">Kembali</a>
            </form>
        </section>
    </main>

</div>
</body>
</html>
