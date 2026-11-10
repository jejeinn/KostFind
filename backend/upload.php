<?php
// backend/upload.php - validasi & penyimpanan foto kost
function simpan_foto(?array $f): ?string {
    if (!$f || $f['error'] === UPLOAD_ERR_NO_FILE) return null;
    if ($f['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Upload foto gagal (kode ' . $f['error'] . ').');
    if ($f['size'] > UPLOAD_MAX_BYTES) throw new RuntimeException('Ukuran foto maksimal 2 MB.');
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime] ?? null;
    if (!$ext || !@getimagesize($f['tmp_name'])) throw new RuntimeException('Foto harus berformat JPG, PNG, atau WEBP.');
    $nama = bin2hex(random_bytes(12)) . '.' . $ext;
    if (!move_uploaded_file($f['tmp_name'], UPLOAD_DIR . $nama)) throw new RuntimeException('Foto gagal disimpan di folder uploads.');
    return $nama;
}
function hapus_foto(?string $n): void {
    if ($n && preg_match('/^[a-f0-9]{24}\.(jpg|png|webp)$/', $n) && is_file(UPLOAD_DIR . $n)) unlink(UPLOAD_DIR . $n);
}
