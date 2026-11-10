<?php
// backend/config.php - pengaturan database (ubah di sini saja)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');   // bawaan XAMPP
define('DB_PASS', '');       // bawaan XAMPP: kosong
define('DB_NAME', 'kost_find');

define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_MAX_BYTES', 2 * 1024 * 1024);          // 2 MB
define('UPLOAD_ALLOWED_MIME', ['image/jpeg', 'image/png', 'image/webp']);
