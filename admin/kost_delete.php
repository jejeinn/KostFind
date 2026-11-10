<?php
require_once 'auth.php'; admin_wajib();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: kost.php'); exit; }
csrf_cek();
$id = (int)($_POST['id'] ?? 0);
$st = $conn->prepare("SELECT foto FROM kost WHERE id = ?"); $st->bind_param('i', $id); $st->execute();
$row = $st->get_result()->fetch_assoc();
if ($row) {
    $d = $conn->prepare("DELETE FROM kost WHERE id = ?"); $d->bind_param('i', $id); $d->execute();
    hapus_foto($row['foto']);
    flash('Data kost berhasil dihapus.');
} else flash('Kost tidak ditemukan.', 'err');
header('Location: kost.php');
