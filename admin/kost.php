<?php
require_once 'auth.php'; admin_wajib();
$cari = trim($_GET['q'] ?? '');
$like = '%' . $cari . '%';
$st = $conn->prepare("SELECT id,nama,alamat,tipe_kost,harga_bulan,jarak_meter,status FROM kost WHERE nama LIKE ? OR alamat LIKE ? ORDER BY id DESC");
$st->bind_param('ss', $like, $like); $st->execute();
$list = $st->get_result()->fetch_all(MYSQLI_ASSOC);
$judul = 'Data Kost'; $menu = 'kost'; include 'header.php';
?>
<div class="panel"><div class="row-between">
  <form class="inline" method="get"><input name="q" value="<?= e($cari) ?>" placeholder="Cari nama/alamat"><button class="btn btn-sm">Cari</button></form>
  <a class="btn" href="kost_form.php"><i class="fa-solid fa-plus"></i> Tambah Kost</a></div>
<div class="scroll"><table class="tbl"><tr><th>#</th><th>Nama</th><th>Alamat</th><th>Jenis</th><th>Harga</th><th>Jarak</th><th>Status</th><th>Aksi</th></tr>
<?php foreach ($list as $i => $k): ?><tr><td><?= $i + 1 ?></td><td><b><?= e($k['nama']) ?></b></td><td><?= e($k['alamat']) ?></td>
  <td><?= e($k['tipe_kost']) ?></td><td><?= rupiah($k['harga_bulan']) ?></td><td><?= jarak_fmt($k['jarak_meter']) ?></td>
  <td><span class="badge <?= e($k['status']) ?>"><?= e($k['status']) ?></span></td>
  <td class="aksi"><a class="btn btn-sm btn-ghost" href="kost_form.php?id=<?= (int)$k['id'] ?>">Edit</a>
    <form method="post" action="kost_delete.php" data-confirm="Hapus kost <?= e($k['nama']) ?>? Data penilaiannya ikut terhapus.">
      <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$k['id'] ?>"><button class="btn btn-sm btn-danger">Hapus</button></form></td></tr>
<?php endforeach; if (!$list): ?><tr><td colspan="8" class="center muted">Belum ada data.</td></tr><?php endif; ?></table></div></div>
<?php include 'footer.php'; ?>
