
<?php
require_once 'koneksi.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    die("ID mata kuliah tidak valid.");
}

$stmt = $conn->prepare("SELECT * FROM mata_kuliah WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die("Data tidak ditemukan.");
}

$error = '';
$pesan = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode = trim($_POST['kode'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $dosen = trim($_POST['dosen'] ?? '');
    $semesterInput = trim($_POST['semester'] ?? '');

    if (
        $kode === '' || $nama === '' || $dosen === '' ||
        !ctype_digit($semesterInput) ||
        (int)$semesterInput < 1 || (int)$semesterInput > 14
    ) {
        $error = "Lengkapi data dengan benar. Semester harus 1–14.";
    } else {
        $semester = (int)$semesterInput;

        // Periksa apakah ada perubahan data
        if (
            $kode === $data['kode'] &&
            $nama === $data['nama'] &&
            $dosen === $data['dosen'] &&
            $semester === (int)$data['semester']
        ) {
            $pesan = "Tidak ada perubahan data.";
        } else {
            $ubah = $conn->prepare(
                "UPDATE mata_kuliah
                 SET kode = ?, nama = ?, dosen = ?, semester = ?
                 WHERE id = ?"
            );
            $ubah->bind_param(
                "sssii",
                $kode,
                $nama,
                $dosen,
                $semester,
                $id
            );

            if ($ubah->execute()) {
                header("Location: matakuliah_index.php");
                exit;
            }

            $error = "Gagal mengubah data. Pastikan kode tidak duplikat.";
        }
    }

    // Pertahankan nilai yang diketik setelah validasi
    $data['kode'] = $kode;
    $data['nama'] = $nama;
    $data['dosen'] = $dosen;
    $data['semester'] = $semesterInput;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Mata Kuliah</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="main-content">
    <h1 class="page-title">Edit Mata Kuliah</h1>

    <section class="content-card">
        <?php if ($error !== ''): ?>
            <p class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <?php if ($pesan !== ''): ?>
            <p class="alert">
                <?= htmlspecialchars($pesan) ?>
            </p>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label class="form-label">Kode</label>
                <input class="form-control" name="kode"
                    value="<?= htmlspecialchars($data['kode']) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Nama Mata Kuliah</label>
                <input class="form-control" name="nama"
                    value="<?= htmlspecialchars($data['nama']) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Dosen</label>
                <input class="form-control" name="dosen"
                    value="<?= htmlspecialchars($data['dosen']) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Semester</label>
                <input class="form-control" type="number" name="semester"
                    min="1" max="14"
                    value="<?= htmlspecialchars((string)$data['semester']) ?>"
                    required>
            </div>

            <button type="submit" class="btn btn-success">
                Simpan Perubahan
            </button>
            <a href="matakuliah_index.php" class="btn btn-secondary">
                Batal
            </a>
        </form>
    </section>
</main>
</body>
</html>
