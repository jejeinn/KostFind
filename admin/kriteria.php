<?php
require_once 'auth.php'; admin_wajib();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    $ids = array_column($conn->query("SELECT id FROM kriteria")->fetch_all(MYSQLI_ASSOC), 'id');
    $total = 0; $data = []; $ok = true;
    foreach ($ids as $kid) {
        $b = $_POST['bobot'][$kid] ?? ''; $t = $_POST['tipe'][$kid] ?? ''; $n = trim($_POST['nama'][$kid] ?? '');
        if (!is_numeric($b) || $b < 0 || $b > 100 || !in_array($t, ['benefit', 'cost'], true) || $n === '' || mb_strlen($n) > 100) { $ok = false; break; }
        $data[$kid] = [(float)$b, $t, $n]; $total += (float)$b;
    }
    if (!$ok) flash('Ada isian yang tidak valid (bobot 0-100, tipe, nama wajib).', 'err');
    elseif (abs($total - 100) > 0.005) flash('Total bobot harus tepat 100%. Saat ini: ' . round($total, 2) . '%.', 'err');
    else {
        $conn->begin_transaction();
        $st = $conn->prepare("UPDATE kriteria SET bobot = ?, tipe = ?, nama = ? WHERE id = ?");
        foreach ($data as $kid => [$b, $t, $n]) { $st->bind_param('dssi', $b, $t, $n, $kid); $st->execute(); }
        $conn->commit(); flash('Bobot dan kriteria disimpan. Perhitungan SAW otomatis memakai bobot baru.');
    }
    header('Location: kriteria.php'); exit;
}
$list = $conn->query("SELECT * FROM kriteria ORDER BY id")->fetch_all(MYSQLI_ASSOC);
$judul = 'Kriteria & Bobot'; $menu = 'kriteria'; include 'header.php';
?>
<form method="post" class="panel"><?= csrf_field() ?>
<p class="muted">Cost: makin kecil makin baik. Benefit: makin besar makin baik. Total bobot harus 100%.</p>
<div class="scroll"><table class="tbl"><tr><th>Kode</th><th>Kelompok</th><th>Nama kriteria</th><th>Tipe</th><th>Bobot (%)</th></tr>
<?php foreach ($list as $k): ?><tr><td><?= e($k['kode']) ?></td><td><?= e($k['kelompok']) ?></td>
  <td><input name="nama[<?= (int)$k['id'] ?>]" value="<?= e($k['nama']) ?>" maxlength="100" required></td>
  <td><select name="tipe[<?= (int)$k['id'] ?>]"><option value="benefit" <?= $k['tipe'] === 'benefit' ? 'selected' : '' ?>>Benefit</option><option value="cost" <?= $k['tipe'] === 'cost' ? 'selected' : '' ?>>Cost</option></select></td>
  <td><input type="number" class="bobot" step="0.01" min="0" max="100" name="bobot[<?= (int)$k['id'] ?>]" value="<?= e($k['bobot']) ?>" required></td></tr>
<?php endforeach; ?></table></div>
<div class="sticky-bar"><span>Total bobot: <b id="totalBobot">0</b>%</span><button class="btn" id="btnBobot"><i class="fa-solid fa-floppy-disk"></i> Simpan</button></div></form>
<?php include 'footer.php'; ?>
