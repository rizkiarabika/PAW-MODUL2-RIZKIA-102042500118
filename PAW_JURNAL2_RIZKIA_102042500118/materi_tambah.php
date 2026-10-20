
<?php
require_once 'koneksi.php';

$mk = '';
$judul = '';
$isi = '';
$tautan = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mk = trim($_POST['mata_kuliah_id'] ?? '');
    $judul = trim($_POST['judul'] ?? '');
    $isi = trim($_POST['isi'] ?? '');
    $tautan = trim($_POST['tautan'] ?? '');

    // Validasi data wajib
    if ($mk === '' || !ctype_digit($mk) || (int) $mk < 1
        || $judul === '' || $isi === '') {
        $error = "Lengkapi semua data wajib dengan benar.";
    } else {
        $mk = (int) $mk;

        // Pastikan mata kuliah tersedia di database
        $cek = $conn->prepare(
            "SELECT id FROM mata_kuliah WHERE id = ?"
        );
        $cek->bind_param("i", $mk);
        $cek->execute();
        $hasil = $cek->get_result();

        if ($hasil->num_rows == 0) {
            $error = "Mata kuliah yang dipilih tidak ditemukan.";
        } elseif (
            $tautan !== '' &&
            (!filter_var($tautan, FILTER_VALIDATE_URL) ||
             !in_array(
                 strtolower(parse_url($tautan, PHP_URL_SCHEME) ?? ''),
                 ['http', 'https'],
                 true
             ))
        ) {
            $error = "Tautan harus berupa URL valid yang diawali http:// atau https://.";
        } else {
            $stmt = $conn->prepare(
                "INSERT INTO materi
                 (mata_kuliah_id, judul, isi, tautan)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("isss", $mk, $judul, $isi, $tautan);

            if ($stmt->execute()) {
                header("Location: materi_index.php");
                exit;
            }

            $error = "Materi gagal disimpan.";
        }
    }
}

$daftar = $conn->query(
    "SELECT id, nama FROM mata_kuliah ORDER BY nama ASC"
);

function h($nilai) {
    return htmlspecialchars((string) $nilai, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Materi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="main-content">
    <h1 class="page-title">Tambah Materi</h1>

    <section class="content-card">
        <?php if ($error !== ''): ?>
            <p class="alert alert-error"><?= h($error) ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label class="form-label">Mata Kuliah</label>
                <select name="mata_kuliah_id" class="form-control" required>
                    <option value="">Pilih Mata Kuliah</option>
                    <?php while ($data = $daftar->fetch_assoc()): ?>
                        <option value="<?= h($data['id']) ?>"
                            <?= (string) $mk === (string) $data['id']
                                ? 'selected' : '' ?>>
                            <?= h($data['nama']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Judul Materi</label>
                <input name="judul" class="form-control"
                       value="<?= h($judul) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Isi Materi</label>
                <textarea name="isi" class="form-control"
                          rows="5" required><?= h($isi) ?></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Tautan (Opsional)</label>
                <input type="url" name="tautan" class="form-control"
                       value="<?= h($tautan) ?>">
            </div>

            <button class="btn btn-success" type="submit">Simpan</button>
            <a href="materi_index.php" class="btn btn-secondary">Batal</a>
        </form>
    </section>
</main>
</body>
</html>
