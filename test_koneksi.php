<?php
// test_koneksi.php - hapus setelah dipakai
require_once __DIR__ . '/koneksi.php';
foreach (['admin','kost','kriteria','opsi_nilai','nilai_kost','fasilitas','kost_fasilitas'] as $t) {
    $n = $conn->query("SELECT COUNT(*) c FROM `$t`")->fetch_assoc()['c'];
    echo "$t: $n baris<br>";
}
$tot = $conn->query("SELECT SUM(bobot) s FROM kriteria")->fetch_assoc()['s'];
echo "<b>Total bobot: $tot</b>";
