<?php
require_once 'auth.php'; admin_wajib();
$id = (int)($_GET['id'] ?? 0);
$tipe_ok = ['Khusus Perempuan', 'Khusus Laki-Laki', 'Campuran'];
$kost = ['nama' => '', 'alamat' => '', 'tipe_kost' => 'Khusus Perempuan', 'harga_bulan' => '', 'jarak_meter' => '', 'deskripsi' => '', 'foto' => null, 'status' => 'aktif'];
$nilai = []; $fasSel = [];
if ($id) {
    $st = $conn->prepare("SELECT * FROM kost WHERE id = ?"); $st->bind_param('i', $id); $st->execute();
    $row = $st->get_result()->fetch_assoc();
    if (!$row) { flash('Kost tidak ditemukan.', 'err'); header('Location: kost.php'); exit; }
    $kost = $row;
    $st = $conn->prepare("SELECT kriteria_id, nilai FROM nilai_kost WHERE kost_id = ?"); $st->bind_param('i', $id); $st->execute();
    foreach ($st->get_result() as $r) $nilai[$r['kriteria_id']] = (int)$r['nilai'];
    $st = $conn->prepare("SELECT fasilitas_id FROM kost_fasilitas WHERE kost_id = ?"); $st->bind_param('i', $id); $st->execute();
    foreach ($st->get_result() as $r) $fasSel[] = (int)$r['fasilitas_id'];
}
$krit = [];
foreach ($conn->query("SELECT k.id,k.nama,k.kelompok,o.label,o.skor FROM kriteria k JOIN opsi_nilai o ON o.kriteria_id=k.id ORDER BY k.id,o.skor") as $r) {
    $krit[$r['id']]['nama'] = $r['nama']; $krit[$r['id']]['kelompok'] = $r['kelompok']; $krit[$r['id']]['opsi'][(int)$r['skor']] = $r['label'];
}
$fasAll = $conn->query("SELECT id, nama FROM fasilitas ORDER BY nama")->fetch_all(MYSQLI_ASSOC);
$err = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    $kost['nama'] = trim($_POST['nama'] ?? ''); $kost['alamat'] = trim($_POST['alamat'] ?? '');
    $kost['tipe_kost'] = $_POST['tipe_kost'] ?? ''; $kost['deskripsi'] = trim($_POST['deskripsi'] ?? '');
    $kost['status'] = $_POST['status'] ?? ''; $kost['harga_bulan'] = $_POST['harga_bulan'] ?? ''; $kost['jarak_meter'] = $_POST['jarak_meter'] ?? '';
    if ($kost['nama'] === '' || mb_strlen($kost['nama']) > 120) $err[] = 'Nama kost wajib diisi (maks 120 karakter).';
    if ($kost['alamat'] === '' || mb_strlen($kost['alamat']) > 255) $err[] = 'Alamat wajib diisi (maks 255 karakter).';
    if (!in_array($kost['tipe_kost'], $tipe_ok, true)) $err[] = 'Jenis kost tidak valid.';
    if (!in_array($kost['status'], ['aktif', 'nonaktif'], true)) $err[] = 'Status tidak valid.';
    if (!ctype_digit((string)$kost['harga_bulan']) || (int)$kost['harga_bulan'] < 1 || (int)$kost['harga_bulan'] > 100000000) $err[] = 'Harga harus angka bulat lebih dari 0.';
    if (!ctype_digit((string)$kost['jarak_meter']) || (int)$kost['jarak_meter'] < 1 || (int)$kost['jarak_meter'] > 100000) $err[] = 'Jarak (meter) harus angka bulat lebih dari 0.';
    if (mb_strlen($kost['deskripsi']) > 3000) $err[] = 'Deskripsi maksimal 3000 karakter.';
    $nilai = [];
    foreach ($krit as $kid => $k) {
        $s = $_POST['nilai'][$kid] ?? '';
        if (!ctype_digit((string)$s) || !isset($k['opsi'][(int)$s])) $err[] = 'Penilaian "' . $k['nama'] . '" wajib dipilih.';
        else $nilai[$kid] = (int)$s;
    }
    $fasSel = array_values(array_unique(array_map('intval', (array)($_POST['fas'] ?? []))));
    if (!$err) {
        $fotoBaru = null; $fotoLama = $kost['foto'];
        try {
            $fotoBaru = simpan_foto($_FILES['foto'] ?? null);
            $namaFoto = $fotoBaru ?? $fotoLama;
            $conn->begin_transaction();
            $h = (int)$kost['harga_bulan']; $j = (int)$kost['jarak_meter'];
            if ($id) {
                $st = $conn->prepare("UPDATE kost SET nama=?,alamat=?,tipe_kost=?,harga_bulan=?,jarak_meter=?,deskripsi=?,foto=?,status=? WHERE id=?");
                $st->bind_param('sssiisssi', $kost['nama'], $kost['alamat'], $kost['tipe_kost'], $h, $j, $kost['deskripsi'], $namaFoto, $kost['status'], $id);
                $st->execute();
            } else {
                $st = $conn->prepare("INSERT INTO kost (nama,alamat,tipe_kost,harga_bulan,jarak_meter,deskripsi,foto,status) VALUES (?,?,?,?,?,?,?,?)");
                $st->bind_param('sssiisss', $kost['nama'], $kost['alamat'], $kost['tipe_kost'], $h, $j, $kost['deskripsi'], $namaFoto, $kost['status']);
                $st->execute(); $id = $conn->insert_id;
            }
            $d = $conn->prepare("DELETE FROM nilai_kost WHERE kost_id = ?"); $d->bind_param('i', $id); $d->execute();
            $d = $conn->prepare("DELETE FROM kost_fasilitas WHERE kost_id = ?"); $d->bind_param('i', $id); $d->execute();
            $ins = $conn->prepare("INSERT INTO nilai_kost (kost_id,kriteria_id,nilai) VALUES (?,?,?)");
            foreach ($nilai as $kid => $s) { $ins->bind_param('iii', $id, $kid, $s); $ins->execute(); }
            $insf = $conn->prepare("INSERT INTO kost_fasilitas (kost_id,fasilitas_id) SELECT ?, id FROM fasilitas WHERE id = ?");
            foreach ($fasSel as $fid) { $insf->bind_param('ii', $id, $fid); $insf->execute(); }
            $conn->commit();
            if ($fotoBaru && $fotoLama) hapus_foto($fotoLama);
            flash('Data kost berhasil disimpan.'); header('Location: kost.php'); exit;
        } catch (RuntimeException $x) {
            $err[] = $x->getMessage();
        } catch (Throwable $x) {
            $conn->rollback(); hapus_foto($fotoBaru);
            $err[] = 'Gagal menyimpan data ke database.';
        }
    }
}
$judul = $id ? 'Edit Kost' : 'Tambah Kost'; $menu = 'kost'; include 'header.php';
?>
<?php if ($err): ?><div class="alert err"><ul><?php foreach ($err as $m): ?><li><?= e($m) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" enctype="multipart/form-data" class="panel">
  <?= csrf_field() ?>
  <h3>Identitas kost</h3>
  <div class="form-grid">
    <label>Nama kost<input name="nama" required maxlength="120" value="<?= e($kost['nama']) ?>"></label>
    <label>Jenis kost<select name="tipe_kost"><?php foreach ($tipe_ok as $t): ?><option <?= $kost['tipe_kost'] === $t ? 'selected' : '' ?>><?= e($t) ?></option><?php endforeach; ?></select></label>
    <label>Harga per bulan (Rp)<input type="number" name="harga_bulan" min="1" required value="<?= e($kost['harga_bulan']) ?>"></label>
    <label>Jarak ke kampus (meter)<input type="number" name="jarak_meter" min="1" required value="<?= e($kost['jarak_meter']) ?>"></label>
    <label>Status<select name="status"><option value="aktif" <?= $kost['status'] === 'aktif' ? 'selected' : '' ?>>aktif</option><option value="nonaktif" <?= $kost['status'] === 'nonaktif' ? 'selected' : '' ?>>nonaktif</option></select></label>
    <label>Foto (JPG/PNG/WEBP, maks 2 MB)<input type="file" name="foto" accept="image/jpeg,image/png,image/webp">
      <?php if ($kost['foto']): ?><small class="muted">Foto saat ini: <img src="../uploads/<?= e($kost['foto']) ?>" class="thumb"></small><?php endif; ?></label>
  </div>
  <label>Alamat<input name="alamat" required maxlength="255" value="<?= e($kost['alamat']) ?>"></label>
  <label>Deskripsi<textarea name="deskripsi" rows="3" maxlength="3000"><?= e($kost['deskripsi']) ?></textarea></label>
  <h3>Fasilitas tambahan</h3>
  <div class="checks"><?php foreach ($fasAll as $f): ?><label class="chk"><input type="checkbox" name="fas[]" value="<?= (int)$f['id'] ?>" <?= in_array((int)$f['id'], $fasSel, true) ? 'checked' : '' ?>> <?= e($f['nama']) ?></label><?php endforeach; ?></div>
  <h3>Penilaian kost (nilai alternatif SAW)</h3>
  <?php $g = ''; foreach ($krit as $kid => $k):
    if ($g !== $k['kelompok']) { if ($g !== '') echo '</div>'; $g = $k['kelompok']; echo '<h4 class="grp-t">' . e($g) . '</h4><div class="form-grid">'; } ?>
    <label><?= e($k['nama']) ?><select name="nilai[<?= (int)$kid ?>]" required><option value="">-- pilih --</option>
      <?php foreach ($k['opsi'] as $skor => $lb): ?><option value="<?= $skor ?>" <?= ($nilai[$kid] ?? null) === $skor ? 'selected' : '' ?>><?= e($lb) ?></option><?php endforeach; ?></select></label>
  <?php endforeach; echo '</div>'; ?>
  <div class="actions"><button class="btn btn-lg"><i class="fa-solid fa-floppy-disk"></i> Simpan</button><a href="kost.php" class="btn btn-ghost btn-lg">Batal</a></div>
</form>
<?php include 'footer.php'; ?>
