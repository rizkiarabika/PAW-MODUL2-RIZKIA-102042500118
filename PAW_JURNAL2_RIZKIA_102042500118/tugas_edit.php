
<?php
require_once 'koneksi.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    die("ID tugas tidak valid.");
}

$stmt = $conn->prepare("SELECT * FROM tugas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$tugas = $stmt->get_result()->fetch_assoc();

if (!$tugas) {
    die("Tugas tidak ditemukan.");
}

$pesan = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mkInput = trim($_POST['mata_kuliah_id'] ?? '');
    $judul = trim($_POST['judul'] ?? '');
    $instruksi = trim($_POST['instruksi'] ?? '');
    $tenggat = trim($_POST['tenggat'] ?? '');
    $status = trim($_POST['status'] ?? '');

    if (
        $mkInput === '' || !ctype_digit($mkInput) ||
        (int)$mkInput < 1 || $judul === '' ||
        $instruksi === '' || $tenggat === ''
    ) {
        $error = "Semua kolom wajib diisi dengan benar.";
    } elseif (!in_array($status, ['Draf', 'Aktif', 'Selesai'], true)) {
        $error = "Status tugas tidak valid.";
    } else {
        $tanggal = DateTime::createFromFormat('!Y-m-d', $tenggat);

        if (!$tanggal || $tanggal->format('Y-m-d') !== $tenggat) {
            $error = "Tanggal tenggat tidak valid.";
        } else {
            $mk = (int)$mkInput;

            $cek = $conn->prepare(
                "SELECT id FROM mata_kuliah WHERE id = ?"
            );
            $cek->bind_param("i", $mk);
            $cek->execute();
            $hasil = $cek->get_result();

            if ($hasil->num_rows == 0) {
                $error = "Mata kuliah tidak ditemukan.";
            } elseif (
                $mk === (int)$tugas['mata_kuliah_id'] &&
                $judul === $tugas['judul'] &&
                $instruksi === $tugas['instruksi'] &&
                $tenggat === $tugas['tenggat'] &&
                $status === $tugas['status']
            ) {
                $pesan = "Tidak ada perubahan data.";
            } else {
                $ubah = $conn->prepare(
                    "UPDATE tugas
                     SET mata_kuliah_id = ?, judul = ?, instruksi = ?,
                         tenggat = ?, status = ?
                     WHERE id = ?"
                );

                $ubah->bind_param(
                    "issssi",
                    $mk, $judul, $instruksi, $tenggat, $status, $id
                );

                if ($ubah->execute()) {
                    header("Location: tugas_index.php");
                    exit;
                }

                $error = "Tugas gagal diubah.";
            }
        }
    }

    // Pertahankan input saat validasi gagal
    $tugas['mata_kuliah_id'] = $mkInput;
    $tugas['judul'] = $judul;
    $tugas['instruksi'] = $instruksi;
    $tugas['tenggat'] = $tenggat;
    $tugas['status'] = $status;
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
    <title>Edit Tugas</title>
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
        <h1 class="page-title">Edit Tugas</h1>

        <?php if ($error !== ''): ?>
            <p class="alert alert-error"><?= h($error) ?></p>
        <?php endif; ?>

        <?php if ($pesan !== ''): ?>
            <p class="alert"><?= h($pesan) ?></p>
        <?php endif; ?>

        <section class="content-card">
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Mata Kuliah</label>
                    <select name="mata_kuliah_id"
                            class="form-control" required>
                        <option value="">Pilih Mata Kuliah</option>
                        <?php while ($mk = $mata_kuliah->fetch_assoc()): ?>
                            <option value="<?= h($mk['id']) ?>"
                                <?= (string)$mk['id'] ===
                                    (string)$tugas['mata_kuliah_id']
                                    ? 'selected' : '' ?>>
                                <?= h($mk['nama']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Judul Tugas</label>
                    <input type="text" name="judul" class="form-control"
                        value="<?= h($tugas['judul']) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Instruksi</label>
                    <textarea name="instruksi" class="form-control"
                        required><?= h($tugas['instruksi']) ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Tenggat</label>
                    <input type="date" name="tenggat" class="form-control"
                        value="<?= h($tugas['tenggat']) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control" required>
                        <?php foreach (['Draf', 'Aktif', 'Selesai'] as $s): ?>
                            <option value="<?= h($s) ?>"
                                <?= $s === $tugas['status']
                                    ? 'selected' : '' ?>>
                                <?= h($s) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">
                    Simpan Perubahan
                </button>
                <a href="tugas_index.php" class="btn btn-secondary">
                    Kembali
                </a>
            </form>
        </section>
    </main>

</div>
</body>
</html>
