<?php
// admin/auth.php - session, proteksi halaman admin, CSRF, flash message
session_start();
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../backend/upload.php';

function admin_wajib(): void {
    if (empty($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
}
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . csrf_token() . '">'; }
function csrf_cek(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(400); exit('Token keamanan tidak valid. Muat ulang halaman.'); }
}
function flash(?string $pesan = null, string $tipe = 'ok') {
    if ($pesan !== null) { $_SESSION['flash'] = [$pesan, $tipe]; return null; }
    $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f;
}
