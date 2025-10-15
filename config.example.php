<?php
// Konfigurasi Database
define('DB_HOST', '....');
define('DB_USER', '....');
define('DB_PASS', '....');
define('DB_NAME', '....');

// Membuat koneksi
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set charset UTF-8
mysqli_set_charset($conn, "utf8");
?>