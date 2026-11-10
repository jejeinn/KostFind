<?php // includes/header.php - variabel: $judul, $halaman ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($judul ?? 'KostFind') ?> | KostFind</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="nav">
  <div class="container nav-in">
    <a href="index.php" class="brand"><i class="fa-solid fa-house-chimney"></i> KostFind</a>
    <button class="nav-toggle" id="navToggle" aria-label="Menu"><i class="fa-solid fa-bars"></i></button>
    <nav id="navMenu">
      <a href="index.php" class="<?= ($halaman ?? '') === 'home' ? 'on' : '' ?>">Beranda</a>
      <a href="cari.php" class="<?= ($halaman ?? '') === 'cari' ? 'on' : '' ?>">Cari Kost</a>
      <a href="admin/login.php" class="btn btn-sm btn-ghost"><i class="fa-solid fa-lock"></i> Admin</a>
    </nav>
  </div>
</header>
