<?php
$host = "localhost";
$username = "root";
$password = "080306";
$database = "paw_jurnal2_lms";

// Membuat koneksi ke database
$conn = new mysqli($host, $username, $password, $database);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Mengatur karakter agar mendukung teks UTF-8
$conn->set_charset("utf8mb4");
?>