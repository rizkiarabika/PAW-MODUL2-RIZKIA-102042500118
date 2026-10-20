
<?php
require_once 'koneksi.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    die("ID materi tidak valid.");
}

$stmt = $conn->prepare("SELECT * FROM materi WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die("Materi tidak ditemukan.");
}

$error = '';
$pesan = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mkInput = trim($_POST['mata_kuliah_id'] ?? '');
    $judul = trim($_POST['judul'] ?? '');
    $isi = trim($_POST['isi'] ?? '');
    $tautan = trim($_POST['tautan'] ?? '');

    if (
        $mkInput === '' || !ctype_digit($mkInput) ||
        (int)$mkInput < 1 || $judul === '' || $isi === ''
    ) {
        $error = "Lengkapi semua data wajib dengan benar.";
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
            $tautan !== '' &&
            (!filter_var($tautan, FILTER_VALIDATE_URL) ||
             !in_array(
                 strtolower(parse_url($tautan, PHP_URL_SCHEME) ?? ''),
                 ['http', 'https'],
                 true
             ))
        ) {
            $error = "Tautan harus berupa URL valid dengan http:// atau https://.";
        } elseif (
            $mk === (int)$data['mata_kuliah_id'] &&
            $judul === $data['judul'] &&
            $isi === $data['isi'] &&
            $tautan === ($data['tautan'] ?? '')
        ) {
            $pesan = "Tidak ada perubahan data.";
        } else {
            $ubah = $conn->prepare(
                "UPDATE materi
                 SET mata_kuliah_id = ?, judul = ?, isi = ?, tautan = ?
                 WHERE id = ?"
            );
            $ubah->bind_param("isssi", $mk, $judul, $isi, $tautan, $id);

            if ($ubah->execute()) {
                header("Location: materi_index.php");
                exit;
            }

            $error = "Materi gagal diperbarui.";
        }
    }

    // Pertahankan isian jika validasi gagal
    $data['mata_kuliah_id'] = $mkInput;
    $data['judul'] = $judul;
    $data['isi'] = $isi;
    $data['tautan'] = $tautan;
}

$daftar = $conn->query(
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
    <title>Edit Materi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="main-content">
    <h1 class="page-title">Edit Materi</h1>

    <section class="content-card">
        <?php if ($error !== ''): ?>
            <p class="alert alert-error"><?= h($error) ?></p>
        <?php endif; ?>

        <?php if ($pesan !== ''): ?>
            <p class="alert"><?= h($pesan) ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label class="form-label">Mata Kuliah</label>
                <select name="mata_kuliah_id" class="form-control" required>
                    <option value="">Pilih Mata Kuliah</option>
                    <?php while ($mk = $daftar->fetch_assoc()): ?>
                        <option value="<?= h($mk['id']) ?>"
                            <?= (string)$mk['id'] ===
                                (string)$data['mata_kuliah_id']
                                ? 'selected' : '' ?>>
                            <?= h($mk['nama']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Judul Materi</label>
                <input name="judul" class="form-control"
                    value="<?= h($data['judul']) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Isi Materi</label>
                <textarea name="isi" class="form-control"
                    rows="5" required><?= h($data['isi']) ?></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Tautan (Opsional)</label>
                <input type="url" name="tautan" class="form-control"
                    value="<?= h($data['tautan'] ?? '') ?>">
            </div>

            <button class="btn btn-success" type="submit">
                Simpan Perubahan
            </button>
            <a href="materi_index.php" class="btn btn-secondary">Batal</a>
        </form>
    </section>
</main>
</body>
</html>
