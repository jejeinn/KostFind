<?php // includes/card.php - kartu kost. $k = data kost; $k['peringkat'] & $k['skor'] opsional
function kost_card(array $k): void { ?>
<article class="card <?= isset($k['peringkat']) && $k['peringkat'] === 1 ? 'card-top' : '' ?>">
  <div class="card-img" <?= $k['foto'] ? 'style="background-image:url(\'uploads/' . e($k['foto']) . '\')"' : '' ?>>
    <?php if (!$k['foto']): ?><i class="fa-solid fa-bed"></i><?php endif; ?>
    <?php if (isset($k['peringkat'])): ?><span class="rank">#<?= (int)$k['peringkat'] ?></span><?php endif; ?>
    <span class="tag"><?= e($k['tipe_kost']) ?></span>
  </div>
  <div class="card-body">
    <h3><?= e($k['nama']) ?></h3>
    <p class="muted"><i class="fa-solid fa-location-dot"></i> <?= e($k['alamat']) ?></p>
    <div class="meta">
      <span><i class="fa-solid fa-tag"></i> <?= rupiah($k['harga_bulan']) ?>/bln</span>
      <span><i class="fa-solid fa-person-walking"></i> <?= jarak_fmt($k['jarak_meter']) ?></span>
    </div>
    <?php if (isset($k['skor'])): ?>
      <div class="score"><div class="score-top"><span>Nilai SAW</span><b><?= number_format($k['skor'], 4, ',', '.') ?></b></div>
      <div class="bar"><i style="width:<?= round($k['skor'] * 100) ?>%"></i></div></div>
    <?php endif; ?>
    <a class="btn btn-block" href="detail.php?id=<?= (int)$k['id'] ?>">Lihat Detail</a>
  </div>
</article>
<?php }
