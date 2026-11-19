<?php
// SALIN file ini menjadi config/koneksi.php lalu isi sesuai server Anda.
// koneksi.php sudah di-ignore oleh .gitignore (tidak ikut ke GitHub).

// --- XAMPP (lokal) ---
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'db_marketplace';

// --- InfinityFree: ganti dengan data dari panel (MySQL Databases) ---
// $DB_HOST = 'xxxxxxxxxxxx';
// $DB_USER = 'if0_xxxxxxxx';
// $DB_PASS = 'PASSWORD_ANDA';
// $DB_NAME = 'if0_XXXXXXXX_marketplace';

mysqli_report(MYSQLI_REPORT_OFF);
$conn = @new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_errno) {
    http_response_code(500);
    exit('Koneksi database gagal.');
}
$conn->set_charset('utf8mb4');
