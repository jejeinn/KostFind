<?php // includes/saw_tabel.php - menampilkan langkah SAW. Butuh $hasil (dari saw_hitung)
$K = $hasil['kriteria']; $A = $hasil['alternatif']; $X = $hasil['matriks']; $R = $hasil['R'];
$tb = array_sum(array_column($K, 'bobot')) ?: 1;
?>
<details class="panel saw" style="margin-top:28px">
  <summary><i class="fa-solid fa-calculator"></i> Lihat langkah perhitungan SAW</summary>
  <p class="formula">Cost: Rij = Min(Xij) / Xij &nbsp;|&nbsp; Benefit: Rij = Xij / Max(Xij) &nbsp;|&nbsp; Vi = &Sigma; (Wj &times; Rij)</p>
  <h4>1. Kriteria, jenis, dan bobot</h4>
  <div class="scroll"><table class="tbl"><tr><th>Kode</th><th>Kriteria</th><th>Jenis</th><th>Bobot Wj</th></tr>
  <?php foreach ($K as $k): ?><tr><td><?= e($k['kode']) ?></td><td><?= e($k['nama']) ?></td><td><?= e($k['tipe']) ?></td><td><?= number_format($k['bobot'] / $tb, 4, ',', '.') ?></td></tr><?php endforeach; ?></table></div>
  <h4>2. Matriks keputusan (Xij)</h4>
  <div class="scroll"><table class="tbl"><tr><th>Kost</th><?php foreach ($K as $k): ?><th><?= e($k['kode']) ?></th><?php endforeach; ?></tr>
  <?php foreach ($A as $a): ?><tr><td><?= e($a['nama']) ?></td><?php foreach ($K as $k): ?><td><?= (float)($X[$a['id']][$k['id']] ?? 0) ?></td><?php endforeach; ?></tr><?php endforeach; ?></table></div>
  <h4>3. Matriks ternormalisasi (Rij)</h4>
  <div class="scroll"><table class="tbl"><tr><th>Kost</th><?php foreach ($K as $k): ?><th><?= e($k['kode']) ?></th><?php endforeach; ?></tr>
  <?php foreach ($A as $a): ?><tr><td><?= e($a['nama']) ?></td><?php foreach ($K as $k): ?><td><?= number_format($R[$a['id']][$k['id']] ?? 0, 3, ',', '.') ?></td><?php endforeach; ?></tr><?php endforeach; ?></table></div>
  <h4>4. Nilai preferensi (Vi) dan peringkat</h4>
  <div class="scroll"><table class="tbl"><tr><th>Peringkat</th><th>Kost</th><th>Vi</th></tr>
  <?php foreach ($A as $a): ?><tr><td><?= (int)$a['peringkat'] ?></td><td><?= e($a['nama']) ?></td><td><b><?= number_format($a['skor'], 4, ',', '.') ?></b></td></tr><?php endforeach; ?></table></div>
</details>
