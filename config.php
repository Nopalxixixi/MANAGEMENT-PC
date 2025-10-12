<?php
// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_daftar_pc');

// Membuat koneksi
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set charset UTF-8
mysqli_set_charset($conn, "utf8");
?>