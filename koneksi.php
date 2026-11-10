<?php
// koneksi.php - dipanggil dengan require_once di semua halaman
require_once __DIR__ . '/backend/config.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    exit('Koneksi database gagal. Pastikan MySQL di XAMPP aktif dan database kost_find sudah di-import.');
}

// Helper agar output aman dari XSS
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function rupiah($n) { return 'Rp ' . number_format((int)$n, 0, ',', '.'); }
function jarak_fmt($m) { $m = (int)$m; return $m < 1000 ? $m . ' m' : number_format($m / 1000, 1, ',', '.') . ' km'; }
