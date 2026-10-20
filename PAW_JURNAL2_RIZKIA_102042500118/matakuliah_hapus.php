<?php
require_once 'koneksi.php';

session_start();

if (
    !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    http_response_code(403);
    die("Permintaan ditolak: token CSRF tidak valid.");
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    die("ID mata kuliah tidak valid.");
}

// Cek apakah mata kuliah tersedia
$stmt = $conn->prepare(
    "SELECT id FROM mata_kuliah WHERE id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$matakuliah = $stmt->get_result()->fetch_assoc();

if (!$matakuliah) {
    die("Mata kuliah tidak ditemukan.");
}

// Cek materi dan tugas yang menggunakan mata kuliah
$stmt = $conn->prepare(
    "SELECT
        (SELECT COUNT(*) FROM materi WHERE mata_kuliah_id = ?) AS jumlah_materi,
        (SELECT COUNT(*) FROM tugas WHERE mata_kuliah_id = ?) AS jumlah_tugas"
);
$stmt->bind_param("ii", $id, $id);
$stmt->execute();
$relasi = $stmt->get_result()->fetch_assoc();

if ($relasi['jumlah_materi'] > 0 || $relasi['jumlah_tugas'] > 0) {
    header("Location: matakuliah_index.php?pesan=gagal_hapus");
    exit;
}

// Hapus jika tidak memiliki materi atau tugas
$stmt = $conn->prepare(
    "DELETE FROM mata_kuliah WHERE id = ?"
);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: matakuliah_index.php?pesan=hapus_berhasil");
    exit;
}

header("Location: matakuliah_index.php?pesan=gagal_hapus");
exit;
?>