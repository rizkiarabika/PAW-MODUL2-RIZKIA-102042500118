
<?php
require_once 'koneksi.php';

$data = $conn->query(
    "SELECT tugas.*, mata_kuliah.nama AS nama_mk
     FROM tugas
     JOIN mata_kuliah ON tugas.mata_kuliah_id = mata_kuliah.id"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Tugas</title>
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
        <h1 class="page-title">Data Tugas</h1>
        <p class="page-description">Daftar tugas dari setiap mata kuliah.</p>

        <section class="content-card">
            <a href="tugas_tambah.php" class="btn">Tambah Tugas</a>
            <br><br>

            <div class="table-wrapper">
                <table class="data-table">
                    <tr>
                        <th>Mata Kuliah</th>
                        <th>Judul Tugas</th>
                        <th>Instruksi</th>
                        <th>Tenggat</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>

                    <?php while ($row = $data->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nama_mk']) ?></td>
                        <td><?= htmlspecialchars($row['judul']) ?></td>
                        <td><?= htmlspecialchars($row['instruksi']) ?></td>
                        <td><?= htmlspecialchars($row['tenggat']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><div style="display: flex; gap: 5px; align-items: center;">
                                <a href="tugas_detail.php?id=<?= (int) $row['id'] ?>"
                                class="btn btn-sm">Detail</a>

                                <a href="tugas_edit.php?id=<?= (int) $row['id'] ?>"
                                class="btn btn-sm btn-success">Edit</a>

                                <form action="tugas_hapus.php" method="POST"
                                    style="margin: 0;"
                                    onsubmit="return confirm('Yakin ingin menghapus tugas ini?')">
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
