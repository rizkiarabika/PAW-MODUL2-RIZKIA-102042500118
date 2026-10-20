
<?php
require_once 'koneksi.php';

// Menghitung jumlah data
$queryMataKuliah = $conn->query("SELECT COUNT(*) AS total FROM mata_kuliah");
$totalMataKuliah = $queryMataKuliah->fetch_assoc()['total'];

$queryMateri = $conn->query("SELECT COUNT(*) AS total FROM materi");
$totalMateri = $queryMateri->fetch_assoc()['total'];

$queryTugas = $conn->query("SELECT COUNT(*) AS total FROM tugas");
$totalTugas = $queryTugas->fetch_assoc()['total'];

// Pencarian mata kuliah
$cari = trim($_GET['cari'] ?? '');

if ($cari !== '') {
    $stmt = $conn->prepare(
        "SELECT id, kode, nama, dosen, semester
         FROM mata_kuliah
         WHERE nama LIKE ? OR kode LIKE ?
         ORDER BY id DESC"
    );

    $keyword = "%" . $cari . "%";
    $stmt->bind_param("ss", $keyword, $keyword);
    $stmt->execute();
    $dataMataKuliah = $stmt->get_result();
} else {
    $dataMataKuliah = $conn->query(
        "SELECT id, kode, nama, dosen, semester
         FROM mata_kuliah
         ORDER BY id DESC
         LIMIT 5"
    );
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
    <title>Dashboard LMS</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="app-layout">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">LMS</div>
        <p class="logo-description">Learning Management System</p>

        <p class="nav-title">MENU UTAMA</p>

       <nav class="nav-menu">
    <a href="index.php" class="active">Dashboard</a>
    <a href="matakuliah_index.php">Mata Kuliah</a>
    <a href="materi_index.php">Materi</a>
    <a href="tugas_index.php">Tugas</a>
</nav>
    </aside>

    <!-- Konten Utama -->
    <main class="main-content">

        <header class="topbar">
            <h1 class="page-title">Dashboard</h1>
            <p class="page-description">
                Selamat datang di Learning Management System.
                Kelola data mata kuliah, materi pembelajaran, dan tugas
                melalui halaman ini.
            </p>
        </header>

        <!-- Statistik -->
        <section class="stats-grid">

            <div class="stat-card">
                <p class="stat-label">Total Mata Kuliah</p>
                <h2 class="stat-number">
                    <?= h($totalMataKuliah) ?>
                </h2>
                <p class="stat-description">
                    Data mata kuliah yang tersimpan
                </p>
                <br><a href="matakuliah_index.php" class="btn btn-sm">
                    Kelola Mata Kuliah
                </a>

            </div>

            <div class="stat-card">
                <p class="stat-label">Total Materi</p>
                <h2 class="stat-number">
                    <?= h($totalMateri) ?>
                </h2>
                <p class="stat-description">
                    Materi pembelajaran yang tersedia
                </p>
                <br>
                <a href="materi_index.php" class="btn btn-sm">
                    Kelola Materi
                </a>
            </div>

            <div class="stat-card">
                <p class="stat-label">Total Tugas</p>
                <h2 class="stat-number">
                    <?= h($totalTugas) ?>
                </h2>
                <p class="stat-description">
                    Tugas yang tercatat di sistem
                </p>
                <br>
                <a href="tugas_index.php" class="btn btn-sm">
                    Kelola Tugas
                </a>
            </div>

        </section>

        <!-- Daftar Mata Kuliah Terbaru -->
        
        <!-- Daftar Mata Kuliah Terbaru -->
        <section class="content-card">

            <div class="section-header">
                <div>
                    <h2 class="section-title">Mata Kuliah Terbaru</h2>
                    <p class="section-description">
                        Cari mata kuliah berdasarkan nama atau kode.
                    </p>
                </div>

                <a href="matakuliah_index.php" class="btn">
                    Lihat Semua
                </a>
            </div>

            <form method="GET" action="index.php"
                style="display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap;">

                <input type="text"
                    name="cari"
                    class="form-control"
                    placeholder="Cari nama atau kode mata kuliah..."
                    value="<?= h($cari) ?>">

                <button type="submit" class="btn">Cari</button>

                <a href="index.php" class="btn btn-secondary">Reset</a>
            </form>

            <?php if ($cari === ''): ?>
                <p class="section-description">
                    Lima data mata kuliah terakhir yang ditambahkan.
                </p>
            <?php else: ?>
                <p class="section-description">
                    Hasil pencarian untuk: <?= h($cari) ?>
                </p>
            <?php endif; ?>

    <div class="table-wrapper">


            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Kode</th>
                            <th>Nama Mata Kuliah</th>
                            <th>Dosen</th>
                            <th>Semester</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if ($dataMataKuliah && $dataMataKuliah->num_rows > 0): ?>
                            <?php $no = 1; ?>

                            <?php while ($row = $dataMataKuliah->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= h($row['kode']) ?></td>
                                    <td><?= h($row['nama']) ?></td>
                                    <td><?= h($row['dosen']) ?></td>
                                    <td><?= h($row['semester']) ?></td>
                                    <td><a href="matakuliah_detail.php?id=<?= $row['id'] ?>"
                                        class="btn btn-sm">Detail</a></td>
                                </tr>
                            <?php endwhile; ?>

                        <?php else: ?>
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        Belum ada data mata kuliah.
                                        Silakan tambahkan data terlebih dahulu.
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </section>

        <footer class="footer">
            <p>Aplikasi LMS Sederhana | Praktikum PAW</p>
        </footer>

    </main>
</div>

</body>
</html>
