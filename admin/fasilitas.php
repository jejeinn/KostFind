<?php
require_once 'auth.php'; admin_wajib();
$ikon_ok = ['fa-circle-check', 'fa-wifi', 'fa-fan', 'fa-snowflake', 'fa-bed', 'fa-bath', 'fa-utensils', 'fa-motorcycle', 'fa-car', 'fa-shirt', 'fa-jug-detergent', 'fa-glass-water', 'fa-couch', 'fa-shield-halved', 'fa-video'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    $aksi = $_POST['aksi'] ?? ''; $id = (int)($_POST['id'] ?? 0);
    $nama = trim($_POST['nama'] ?? ''); $ikon = $_POST['ikon'] ?? 'fa-circle-check';
    try {
        if ($aksi === 'hapus') {
            $st = $conn->prepare("DELETE FROM fasilitas WHERE id = ?"); $st->bind_param('i', $id); $st->execute(); flash('Fasilitas dihapus.');
        } elseif ($nama === '' || mb_strlen($nama) > 80 || !in_array($ikon, $ikon_ok, true)) {
            flash('Nama wajib diisi (maks 80 karakter) dan ikon harus valid.', 'err');
        } elseif ($aksi === 'tambah') {
            $st = $conn->prepare("INSERT INTO fasilitas (nama, ikon) VALUES (?, ?)"); $st->bind_param('ss', $nama, $ikon); $st->execute(); flash('Fasilitas ditambahkan.');
        } elseif ($aksi === 'ubah') {
            $st = $conn->prepare("UPDATE fasilitas SET nama = ?, ikon = ? WHERE id = ?"); $st->bind_param('ssi', $nama, $ikon, $id); $st->execute(); flash('Fasilitas diperbarui.');
        }
    } catch (mysqli_sql_exception $x) {
        flash($x->getCode() == 1062 ? 'Nama fasilitas sudah ada.' : 'Operasi database gagal.', 'err');
    }
    header('Location: fasilitas.php'); exit;
}
$list = $conn->query("SELECT f.*, (SELECT COUNT(*) FROM kost_fasilitas kf WHERE kf.fasilitas_id = f.id) pakai FROM fasilitas f ORDER BY nama")->fetch_all(MYSQLI_ASSOC);
$judul = 'Manajemen Fasilitas'; $menu = 'fasilitas'; include 'header.php';
function opsi_ikon($ok, $sel) { foreach ($ok as $i) echo '<option value="' . $i . '"' . ($i === $sel ? ' selected' : '') . '>' . substr($i, 3) . '</option>'; }
?>
<div class="panel"><h3>Tambah fasilitas</h3>
<form method="post" class="inline"><?= csrf_field() ?><input type="hidden" name="aksi" value="tambah">
  <input name="nama" placeholder="Nama fasilitas" required maxlength="80"><select name="ikon"><?php opsi_ikon($ikon_ok, 'fa-circle-check'); ?></select>
  <button class="btn"><i class="fa-solid fa-plus"></i> Tambah</button></form>
<p class="muted">Fasilitas tambahan tampil di detail kost dan bisa dipilih user sebagai syarat pencarian.</p></div>
<div class="panel"><div class="scroll"><table class="tbl"><tr><th>Ikon</th><th>Nama & ikon</th><th>Dipakai</th><th>Aksi</th></tr>
<?php foreach ($list as $f): ?><tr><td><i class="fa-solid <?= e($f['ikon']) ?>"></i></td>
  <td><form method="post" class="inline"><?= csrf_field() ?><input type="hidden" name="aksi" value="ubah"><input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
    <input name="nama" value="<?= e($f['nama']) ?>" required maxlength="80"><select name="ikon"><?php opsi_ikon($ikon_ok, $f['ikon']); ?></select><button class="btn btn-sm">Simpan</button></form></td>
  <td><?= (int)$f['pakai'] ?> kost</td>
  <td><form method="post" data-confirm="Hapus fasilitas <?= e($f['nama']) ?>?"><?= csrf_field() ?><input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?= (int)$f['id'] ?>"><button class="btn btn-sm btn-danger">Hapus</button></form></td></tr>
<?php endforeach; ?></table></div></div>
<?php include 'footer.php'; ?>
