<?php // admin/header.php - variabel: $judul, $menu
$f = flash();
$nav = ['dashboard' => ['Dashboard', 'fa-gauge'], 'kost' => ['Data Kost', 'fa-building'],
        'fasilitas' => ['Fasilitas', 'fa-couch'], 'kriteria' => ['Kriteria & Bobot', 'fa-scale-balanced']];
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($judul) ?> | Admin KostFind</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="../css/style.css"><link rel="stylesheet" href="../css/admin.css"></head>
<body><div class="wrap">
<aside class="side"><div class="brand"><i class="fa-solid fa-house-chimney"></i> KostFind</div>
  <?php foreach ($nav as $k => [$t, $i]): ?>
    <a href="<?= $k ?>.php" class="<?= $menu === $k ? 'on' : '' ?>"><i class="fa-solid <?= $i ?>"></i> <?= $t ?></a>
  <?php endforeach; ?>
  <a href="../index.php" target="_blank"><i class="fa-solid fa-globe"></i> Lihat Situs</a>
  <a href="logout.php" class="out">
    <i class="fa-solid fa-right-from-bracket"></i>
    Logout (<?= e($_SESSION['admin_nama'] ?? 'Admin') ?>)
</a>
</aside>
<main class="main"><h1 class="title"><?= e($judul) ?></h1>
<?php if ($f): ?><div class="alert <?= e($f[1]) ?>"><?= e($f[0]) ?></div><?php endif; ?>
