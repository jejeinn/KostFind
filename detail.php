<?php
require_once 'koneksi.php';
require_once 'backend/get_kost.php';
$k = get_kost($conn, (int)($_GET['id'] ?? 0));
if (!$k) { http_response_code(404); $judul = 'Tidak ditemukan'; include 'includes/header.php';
  echo '<main class="container section"><div class="empty"><h3>Kost tidak ditemukan</h3><a class="btn" href="cari.php">Cari kost</a></div></main>';
  include 'includes/footer.php'; exit; }
$judul = $k['nama']; $halaman = 'cari';
$fas = get_fasilitas_kost($conn, $k['id']);
$nilai = get_penilaian_kost($conn, $k['id']);
include 'includes/header.php';
?>
<main class="container section">
  <a href="javascript:history.back()" class="muted"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
  <div class="detail">
    <div class="detail-img" <?= $k['foto'] ? 'style="background-image:url(\'uploads/' . e($k['foto']) . '\')"' : '' ?>>
      <?php if (!$k['foto']): ?><i class="fa-solid fa-bed"></i><?php endif; ?></div>
    <div>
      <span class="tag static"><?= e($k['tipe_kost']) ?></span>
      <h1><?= e($k['nama']) ?></h1>
      <p class="muted"><i class="fa-solid fa-location-dot"></i> <?= e($k['alamat']) ?></p>
      <div class="meta big">
        <span><i class="fa-solid fa-tag"></i> <?= rupiah($k['harga_bulan']) ?>/bulan</span>
        <span><i class="fa-solid fa-person-walking"></i> <?= jarak_fmt($k['jarak_meter']) ?> dari kampus</span>
      </div>
      <p><?= nl2br(e($k['deskripsi'] ?: 'Belum ada deskripsi.')) ?></p>
      <?php if ($fas): ?><h4>Fasilitas tambahan</h4>
        <div class="fas"><?php foreach ($fas as $f): ?><span><i class="fa-solid <?= e($f['ikon']) ?>"></i> <?= e($f['nama']) ?></span><?php endforeach; ?></div>
      <?php endif; ?>
    </div>
  </div>
  <h2 class="title">Penilaian kost</h2>
  <div class="grid grid-2">
    <?php foreach ($nilai as $grup => $list): ?>
      <div class="panel"><h3><?= e($grup) ?></h3>
        <table class="mini"><?php foreach ($list as $r): ?>
          <tr><td><?= e($r['nama']) ?></td><td><b><?= e($r['label'] ?? '-') ?></b></td></tr>
        <?php endforeach; ?></table></div>
    <?php endforeach; ?>
  </div>
</main>
<?php include 'includes/footer.php'; ?>
