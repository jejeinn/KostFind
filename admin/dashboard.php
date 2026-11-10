<?php
require_once 'auth.php'; admin_wajib();
$q = fn($s) => (int)$conn->query($s)->fetch_row()[0];
$total = $q("SELECT COUNT(*) FROM kost"); $aktif = $q("SELECT COUNT(*) FROM kost WHERE status='aktif'");
$nfas = $q("SELECT COUNT(*) FROM fasilitas"); $nkrit = $q("SELECT COUNT(*) FROM kriteria");
$bobot = (float)$conn->query("SELECT SUM(bobot) FROM kriteria")->fetch_row()[0];
$rata = (float)$conn->query("SELECT AVG(harga_bulan) FROM kost WHERE status='aktif'")->fetch_row()[0];
$list = $conn->query("SELECT id,nama,tipe_kost,harga_bulan,jarak_meter,status FROM kost ORDER BY id DESC LIMIT 8")->fetch_all(MYSQLI_ASSOC);
$judul = 'Dashboard'; $menu = 'dashboard'; include 'header.php';
?>
<div class="stat-grid">
  <div class="stat"><i class="fa-solid fa-building"></i><b><?= $total ?></b><span>Seluruh kost</span></div>
  <div class="stat"><i class="fa-solid fa-circle-check"></i><b><?= $aktif ?></b><span>Kost aktif</span></div>
  <div class="stat"><i class="fa-solid fa-couch"></i><b><?= $nfas ?></b><span>Fasilitas</span></div>
  <div class="stat"><i class="fa-solid fa-scale-balanced"></i><b><?= $nkrit ?></b><span>Kriteria</span></div>
</div>
<?php if (abs($bobot - 100) > 0.005): ?><div class="alert err">Total bobot kriteria <?= $bobot ?>%, harus 100%. <a href="kriteria.php">Perbaiki</a></div><?php endif; ?>
<div class="panel"><div class="row-between"><h3>Ringkasan data kost <small class="muted">(rata-rata harga aktif <?= rupiah($rata) ?>)</small></h3>
  <a class="btn btn-sm" href="kost_form.php"><i class="fa-solid fa-plus"></i> Tambah</a></div>
  <div class="scroll"><table class="tbl"><tr><th>Nama</th><th>Jenis</th><th>Harga</th><th>Jarak</th><th>Status</th><th></th></tr>
  <?php foreach ($list as $k): ?><tr><td><?= e($k['nama']) ?></td><td><?= e($k['tipe_kost']) ?></td><td><?= rupiah($k['harga_bulan']) ?></td>
    <td><?= jarak_fmt($k['jarak_meter']) ?></td><td><span class="badge <?= e($k['status']) ?>"><?= e($k['status']) ?></span></td>
    <td><a href="kost_form.php?id=<?= (int)$k['id'] ?>">Edit</a></td></tr><?php endforeach; ?></table></div></div>
<?php include 'footer.php'; ?>
