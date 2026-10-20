
<?php
require_once 'koneksi.php';

$error = '';
$kode = '';
$nama = '';
$dosen = '';
$semester = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode = trim($_POST['kode'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $dosen = trim($_POST['dosen'] ?? '');
    $inputSemester = $_POST['semester'] ?? '';

    if (
        $kode === '' || $nama === '' || $dosen === '' ||
        $inputSemester === '' ||
        !ctype_digit((string) $inputSemester) ||
        (int) $inputSemester < 1 ||
        (int) $inputSemester > 14
    ) {
        $error = "Lengkapi semua data dan masukkan semester 1–14.";
    } else {
        $semester = (int) $inputSemester;

        // Periksa kode mata kuliah
        $stmt = $conn->prepare(
            "SELECT id FROM mata_kuliah WHERE kode = ?"
        );
        $stmt->bind_param("s", $kode);
        $stmt->execute();
        $hasilKode = $stmt->get_result();

        // Periksa nama mata kuliah
        $stmtNama = $conn->prepare(
            "SELECT id FROM mata_kuliah WHERE LOWER(nama) = LOWER(?)"
        );
        $stmtNama->bind_param("s", $nama);
        $stmtNama->execute();
        $hasilNama = $stmtNama->get_result();

        if ($hasilKode->num_rows > 0) {
            $error = "Kode mata kuliah sudah terdaftar.";
        } elseif ($hasilNama->num_rows > 0) {
            $error = "Nama mata kuliah sudah terdaftar.";
        } else {
            $stmtTambah = $conn->prepare(
                "INSERT INTO mata_kuliah
                (kode, nama, dosen, semester)
                VALUES (?, ?, ?, ?)"
            );
            $stmtTambah->bind_param(
                "sssi", $kode, $nama, $dosen, $semester
            );

            if ($stmtTambah->execute()) {
                header("Location: matakuliah_index.php");
                exit;
            } else {
                $error = "Data gagal disimpan. Silakan coba lagi.";
            }
        }
    }
}

function h($nilai) {
    return htmlspecialchars((string) $nilai, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Mata Kuliah</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="main-content">
    <h1 class="page-title">Tambah Mata Kuliah</h1>

    <section class="content-card">
        <?php if ($error !== ''): ?>
            <p class="alert alert-error"><?= h($error) ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label class="form-label">Kode Mata Kuliah</label>
                <input class="form-control" name="kode"
                       value="<?= h($kode) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Nama Mata Kuliah</label>
                <input class="form-control" name="nama"
                       value="<?= h($nama) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Nama Dosen</label>
                <input class="form-control" name="dosen"
                       value="<?= h($dosen) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Semester (1–14)</label>
                <input class="form-control" type="number"
                       name="semester" min="1" max="14"
                       value="<?= h($semester) ?>" required>
            </div>

            <button class="btn btn-success" type="submit">
                Simpan
            </button>
            <a href="matakuliah_index.php"
               class="btn btn-secondary">Batal</a>
        </form>
    </section>
</main>
</body>
</html>
