<?php
require_once 'koneksi.php';
require_once 'includes/card.php';
$judul = 'Beranda'; $halaman = 'home';
$n_kost = $conn->query("SELECT COUNT(*) c FROM kost WHERE status='aktif'")->fetch_assoc()['c'];
$n_krit = $conn->query("SELECT COUNT(*) c FROM kriteria")->fetch_assoc()['c'];
$terbaru = $conn->query("SELECT * FROM kost WHERE status='aktif' ORDER BY id DESC LIMIT 3")->fetch_all(MYSQLI_ASSOC);
include 'includes/header.php';
?>
<section class="hero"><div class="container hero-in">
  <div>
    <span class="pill">Khusus mahasiswa Samarinda</span>
    <h1>Temukan kost yang <span>paling cocok</span> untukmu</h1>
    <p>KostFind menghitung rekomendasi kost dari anggaran, jarak, fasilitas, keamanan, kebersihan, kenyamanan, dan peraturan menggunakan metode SAW.</p>
    <a href="cari.php" class="btn btn-lg"><i class="fa-solid fa-magnifying-glass"></i> Mulai Cari Kost</a>
  </div>
  <div class="stats">
    <div><b><?= (int)$n_kost ?></b><span>Kost terdata</span></div>
    <div><b><?= (int)$n_krit ?></b><span>Kriteria penilaian</span></div>
  </div>
</div></section>
<section class="container section">
  <h2 class="title">Cara kerja</h2>
  <div class="steps">
    <div><i class="fa-solid fa-sliders"></i><h4>1. Isi preferensi</h4><p>Tentukan anggaran, jarak, tipe kost, dan kebutuhan minimal.</p></div>
    <div><i class="fa-solid fa-calculator"></i><h4>2. Dihitung dengan SAW</h4><p>Setiap kost dinormalisasi dan diberi nilai berdasarkan bobot kriteria.</p></div>
    <div><i class="fa-solid fa-ranking-star"></i><h4>3. Lihat peringkat</h4><p>Kost diurutkan dari nilai tertinggi ke terendah.</p></div>
  </div>
</section>
<section class="container section">
  <h2 class="title">Kost terbaru</h2>
  <div class="grid"><?php foreach ($terbaru as $k) kost_card($k); ?></div>
</section>
<?php include 'includes/footer.php'; ?>
