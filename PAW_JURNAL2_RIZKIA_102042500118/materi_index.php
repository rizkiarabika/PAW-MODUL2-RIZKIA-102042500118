
<?php
require_once 'koneksi.php';

$data = $conn->query(
    "SELECT materi.*, mata_kuliah.nama AS nama_mk
     FROM materi
     JOIN mata_kuliah ON materi.mata_kuliah_id = mata_kuliah.id"
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Materi</title>
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
            <a href="materi_index.php" class="active">Materi</a>
            <a href="tugas_index.php">Tugas</a>
        </nav>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Data Materi</h1>
        <p class="page-description">Daftar materi pembelajaran.</p>

        <section class="content-card">
            <a href="materi_tambah.php" class="btn">Tambah Materi</a>
            <br><br>
            <div class="table-wrapper">
                <table class="data-table">
                    <tr>
                        <th>Mata Kuliah</th>
                        <th>Judul Materi</th>
                        <th>Isi</th>
                        <th>Aksi</th>
                    </tr>
                    <?php while ($row = $data->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nama_mk']) ?></td>
                        <td><?= htmlspecialchars($row['judul']) ?></td>
                        <td><?= htmlspecialchars($row['isi']) ?></td>
                        <td>
                            <div style="display: flex; flex-direction: row; gap: 5px; align-items: center; white-space: nowrap;">
                                <a href="materi_detail.php?id=<?= (int) $row['id'] ?>"
                                class="btn btn-sm">Detail</a>

                                <a href="materi_edit.php?id=<?= (int) $row['id'] ?>"
                                class="btn btn-sm btn-success">Edit</a>

                                <form action="materi_hapus.php" method="POST"
                                    style="margin: 0; display: inline;"
                                    onsubmit="return confirm('Yakin ingin menghapus materi ini?')">
                                    <input type="hidden" name="id"
                                        value="<?= (int) $row['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        Hapus
                                    </button>
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
