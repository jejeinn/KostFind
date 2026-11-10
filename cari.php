<?php
require_once 'koneksi.php';
$judul = 'Cari Kost'; $halaman = 'cari';
// Form dibuat dari database: pilihan jawaban Google Form (opsi_nilai) per kriteria
$rows = $conn->query("SELECT k.id, k.nama, k.kelompok, o.label, o.skor FROM kriteria k
    JOIN opsi_nilai o ON o.kriteria_id = k.id ORDER BY k.id, o.skor")->fetch_all(MYSQLI_ASSOC);
$grup = [];
foreach ($rows as $r) $grup[$r['kelompok']][$r['id']]['nama'] = $r['nama'];
foreach ($rows as $r) $grup[$r['kelompok']][$r['id']]['opsi'][] = $r;
require_once 'backend/get_fasilitas.php';
$fasAll = get_semua_fasilitas($conn);
include 'includes/header.php';
?>
<main class="container section">
  <h1 class="title">Cari Kost Sesuai Kebutuhanmu</h1>
  <p class="muted">Kosongkan atau pilih "Bebas" untuk preferensi yang tidak penting bagimu.</p>
  <form action="hasil.php" method="get" class="panel" id="formCari">
    <h3><i class="fa-solid fa-wallet"></i> Anggaran, jarak, dan tipe</h3>
    <div class="form-grid">
      <label>Anggaran maksimal per bulan (Rp)
        <input type="number" name="harga_max" id="harga_max" min="0" step="50000" placeholder="Tanpa batas">
        <span class="chips">
          <button type="button" data-set="600000">&lt; 600 rb</button>
          <button type="button" data-set="850000">&lt; 850 rb</button>
          <button type="button" data-set="1500000">&lt; 1,5 jt</button>
          <button type="button" data-set="2000000">&le; 2 jt</button>
        </span></label>
      <label>Jarak maksimal ke kampus
        <select name="jarak_max">
          <option value="0">Bebas</option><option value="500">&lt; 500 m</option>
          <option value="1000">&lt; 1 km</option><option value="2000">&lt; 2 km</option>
          <option value="3000">&lt; 3 km</option></select></label>
      <label>Jenis kost
        <select name="tipe"><option value="">Semua</option><option>Khusus Perempuan</option>
          <option>Khusus Laki-Laki</option><option>Campuran</option></select></label>
    </div>
    <?php if ($fasAll): ?>
    <details class="grp" open>
      <summary><i class="fa-solid fa-chevron-down"></i> Fasilitas tambahan <small>(harus tersedia)</small></summary>
      <div class="checks"><?php foreach ($fasAll as $fs): ?><label class="chk"><input type="checkbox" name="fas[]" value="<?= (int)$fs['id'] ?>"> <i class="fa-solid <?= e($fs['ikon']) ?>"></i> <?= e($fs['nama']) ?></label><?php endforeach; ?></div>
    </details>
    <?php endif; ?>
    <?php foreach ($grup as $nama_grup => $items): ?>
    <details class="grp">
      <summary><i class="fa-solid fa-chevron-down"></i> <?= e($nama_grup) ?> <small>(minimal yang diinginkan)</small></summary>
      <div class="form-grid">
      <?php foreach ($items as $kid => $it): ?>
        <label><?= e($it['nama']) ?>
          <select name="min[<?= (int)$kid ?>]">
            <option value="0">Bebas</option>
            <?php foreach (array_slice($it['opsi'], 1) as $o): ?>
              <option value="<?= (int)$o['skor'] ?>">Minimal: <?= e($o['label']) ?></option>
            <?php endforeach; ?>
          </select></label>
      <?php endforeach; ?>
      </div>
    </details>
    <?php endforeach; ?>
    <div class="actions">
      <button type="submit" class="btn btn-lg"><i class="fa-solid fa-wand-magic-sparkles"></i> Tampilkan Rekomendasi</button>
      <button type="reset" class="btn btn-ghost btn-lg">Reset</button>
    </div>
  </form>
</main>
<?php include 'includes/footer.php'; ?>
