
-- Membuat database LMS
CREATE DATABASE IF NOT EXISTS paw_jurnal2_lms;
USE paw_jurnal2_lms;

-- 1. Tabel Mata Kuliah
CREATE TABLE mata_kuliah (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    dosen VARCHAR(100) NOT NULL,
    semester TINYINT UNSIGNED NOT NULL,
    CHECK (semester BETWEEN 1 AND 14)
) ENGINE=InnoDB;

-- 2. Tabel Materi
CREATE TABLE materi (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mata_kuliah_id INT UNSIGNED NOT NULL,
    judul VARCHAR(150) NOT NULL,
    isi TEXT NOT NULL,
    tautan VARCHAR(255) DEFAULT NULL,

    CONSTRAINT fk_materi_mata_kuliah
        FOREIGN KEY (mata_kuliah_id)
        REFERENCES mata_kuliah(id)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT
) ENGINE=InnoDB;

-- 3. Tabel Tugas
CREATE TABLE tugas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mata_kuliah_id INT UNSIGNED NOT NULL,
    judul VARCHAR(150) NOT NULL,
    instruksi TEXT NOT NULL,
    tenggat DATE NOT NULL,
    status VARCHAR(20) NOT NULL,

    CONSTRAINT fk_tugas_mata_kuliah
        FOREIGN KEY (mata_kuliah_id)
        REFERENCES mata_kuliah(id)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT,

    CHECK (status IN ('Draf', 'Aktif', 'Selesai'))
) ENGINE=InnoDB;

-- DATA AWAL MATA KULIAH
INSERT INTO mata_kuliah
(kode, nama, dosen, semester)
VALUES
('PAW-01', 'Pemrograman Aplikasi Web', 'Budi Santoso', 3),
('BD-01', 'Basis Data', 'Siti Aminah', 2);

-- DATA AWAL MATERI
INSERT INTO materi
(mata_kuliah_id, judul, isi, tautan)
VALUES
(1, 'Dasar PHP',
 'Pengenalan PHP dan sintaks dasar pemrograman web.',
 'https://www.php.net/manual/en/'),

(2, 'Dasar SQL',
 'Pengenalan query SELECT, INSERT, UPDATE, dan DELETE.',
 'https://dev.mysql.com/doc/');

-- DATA AWAL TUGAS
INSERT INTO tugas
(mata_kuliah_id, judul, instruksi, tenggat, status)
VALUES
(1, 'Latihan CRUD',
 'Buat aplikasi tambah, tampil, ubah, dan hapus data.',
 '2026-11-01', 'Aktif'),

(2, 'Latihan Query',
 'Buat query untuk menampilkan dan mengolah data.',
 '2026-11-05', 'Draf');
