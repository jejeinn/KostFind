<?php
require_once 'auth.php';
if (!empty($_SESSION['admin_id'])) { header('Location: dashboard.php'); exit; }
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    $u = trim($_POST['username'] ?? ''); $p = $_POST['password'] ?? '';
    $row = null;
    if ($u !== '' && $p !== '' && strlen($u) <= 50) {
        $st = $conn->prepare("SELECT id, password, nama FROM admin WHERE username = ?");
        $st->bind_param('s', $u); $st->execute();
        $row = $st->get_result()->fetch_assoc();
    }
    if ($row && password_verify($p, $row['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int)$row['id']; $_SESSION['admin_nama'] = $row['nama'];
        header('Location: dashboard.php'); exit;
    }
    usleep(500000);
    $err = 'Username atau password salah.';
}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login Admin | KostFind</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="../css/style.css"><link rel="stylesheet" href="../css/admin.css"></head>
<body class="login-body"><form method="post" class="login-box">
  <h1><i class="fa-solid fa-house-chimney"></i> KostFind</h1><p class="muted">Login Administrator</p>
  <?php if ($err): ?><div class="alert err"><?= e($err) ?></div><?php endif; ?>
  <?= csrf_field() ?>
  <label>Username<input name="username" required maxlength="50" autofocus></label>
  <label>Password<input type="password" name="password" required></label>
  <button class="btn btn-block btn-lg">Masuk</button>
  <a href="../index.php" class="muted center">&larr; Kembali ke situs</a>
</form></body></html>
