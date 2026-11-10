<?php
require_once 'koneksi.php';
require_once 'backend/proses_saw.php';
require_once 'includes/card.php';
$judul = 'Hasil Rekomendasi'; $halaman = 'cari';
$f = baca_filter($_GET);
$hasil = saw_hitung($conn, $f);
$alt = $hasil['alternatif'];
include 'includes/header.php';
?>
<main class="container section">
  <h1 class="title">Hasil Rekomendasi Kost</h1>
  <div class="chips-info">
    <span><i class="fa-solid fa-wallet"></i> <?= $f['harga_max'] ? 'Maks ' . rupiah($f['harga_max']) : 'Anggaran bebas' ?></span>
    <span><i class="fa-solid fa-person-walking"></i> <?= $f['jarak_max'] ? 'Maks ' . jarak_fmt($f['jarak_max']) : 'Jarak bebas' ?></span>
    <span><i class="fa-solid fa-venus-mars"></i> <?= e($f['tipe'] ?: 'Semua jenis kost') ?></span>
    <span><i class="fa-solid fa-filter"></i> <?= count($f['min']) + count($f['fas']) ?> syarat minimal</span>
    <a href="cari.php" class="btn btn-sm btn-ghost">Ubah preferensi</a>
  </div>
  <?php if (!$alt): ?>
    <div class="empty"><i class="fa-regular fa-face-frown"></i><h3>Belum ada kost yang cocok</h3>
      <p>Longgarkan anggaran, jarak, atau syarat minimal lalu coba lagi.</p>
      <a href="cari.php" class="btn">Kembali ke form</a></div>
  <?php else: ?>
    <p class="muted"><?= count($alt) ?> kost ditemukan, diurutkan dari nilai SAW tertinggi.</p>
    <div class="grid"><?php foreach ($alt as $k) kost_card($k); ?></div>
  <?php include 'includes/saw_tabel.php'; endif; ?>
</main>
<?php include 'includes/footer.php'; ?>
