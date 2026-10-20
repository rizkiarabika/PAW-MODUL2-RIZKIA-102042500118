

<?php
require_once 'koneksi.php';

session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$cari = trim($_GET['cari'] ?? '');

if ($cari != '') {
    $stmt = $conn->prepare(
        "SELECT * FROM mata_kuliah
         WHERE nama LIKE ? OR kode LIKE ?
         ORDER BY id DESC"
    );

    $keyword = "%$cari%";
    $stmt->bind_param("ss", $keyword, $keyword);
    $stmt->execute();
    $data = $stmt->get_result();
} else {
    $data = $conn->query(
        "SELECT * FROM mata_kuliah ORDER BY id DESC"
    );
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Mata Kuliah</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="app-layout">
    <aside class="sidebar">
        <h2 class="logo">LMS</h2>
        <p class="logo-description">Learning Management System</p>
        <nav class="nav-menu">
            <a href="index.php">Dashboard</a>
            <a href="matakuliah_index.php" class="active">Mata Kuliah</a>
            <a href="materi_index.php">Materi</a>
            <a href="tugas_index.php">Tugas</a>
        </nav>
    </aside>

   <main class="main-content">
    <h1 class="page-title">Data Mata Kuliah</h1>
    <p class="page-description">Daftar mata kuliah yang tersedia.</p>

    <!-- Pesan setelah proses hapus -->
    <?php if (isset($_GET['pesan'])): ?>
        <?php if ($_GET['pesan'] == 'gagal_hapus'): ?>
            <p class="alert alert-error">
                Mata kuliah tidak dapat dihapus karena masih digunakan oleh materi atau tugas.
            </p>
        <?php elseif ($_GET['pesan'] == 'hapus_berhasil'): ?>
            <p class="alert">
                Mata kuliah berhasil dihapus.
            </p>
        <?php endif; ?>
    <?php endif; ?>

        <section class="content-card">
            
<div style="display: flex; justify-content: space-between;
            align-items: center; gap: 10px; flex-wrap: wrap;">
                <a href="matakuliah_tambah.php" class="btn">
                    Tambah Mata Kuliah
                </a>

                <form method="GET" style="display: flex; gap: 5px;">
                    <input type="text" name="cari"
                        class="form-control"
                        placeholder="Cari nama atau kode..."
                        value="<?= htmlspecialchars($cari) ?>">

                    <button type="submit" class="btn">
                        Cari
                    </button>

                    <a href="matakuliah_index.php" class="btn btn-secondary">
                        Reset
                    </a>
                </form>

            </div>
            <br>
            <div class="table-wrapper">
                <table class="data-table">
                    <tr>
                        <th>Kode</th><th>Nama</th><th>Dosen</th><th>Semester</th><th>Aksi</th>
                    </tr>
                    <?php while ($row = $data->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['kode']) ?></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['dosen']) ?></td>
                        <td><?= $row['semester'] ?></td>
                        <td>
                        <div style="display: flex; gap: 5px; align-items: center;">
                            <a href="matakuliah_detail.php?id=<?= (int) $row['id'] ?>"
                            class="btn btn-sm">Detail</a>

                            <a href="matakuliah_edit.php?id=<?= (int) $row['id'] ?>"
                            class="btn btn-sm btn-success">Edit</a>

                            <form action="matakuliah_hapus.php" method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus mata kuliah ini?')"
                                style="margin: 0;">
                                <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                <input type="hidden" name="csrf_token"
                                    value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </div>
                    </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </section>
    </main>
</div>
</body>
</html>
