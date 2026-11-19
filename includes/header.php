<?php
require_once __DIR__ . '/functions.php';
$base = $base ?? '';
$flash = flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($judul ?? 'Bloomé Flower Shop') ?> | Bloomé</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= $base ?>assets/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg sticky-top"><div class="container">
<a class="navbar-brand" href="<?= $base ?>index.php"><span class="brand-flower">✿</span>Bloomé</a>
<div class="nav-actions ms-auto">
<a class="btn btn-light btn-sm" href="<?= $base ?>index.php">Explore flowers</a>
<?php if (is_admin()): ?>
<a class="btn btn-outline-success btn-sm" href="<?= $base ?>admin/produk.php">Dashboard Admin</a>
<a class="btn btn-outline-danger btn-sm" href="<?= $base ?>admin/logout.php">Logout</a>
<?php else: ?>
<a class="btn btn-success btn-sm" href="<?= $base ?>keranjang.php">♡ Keranjang <span class="badge ms-1"><?= cart_count() ?></span></a>
<a class="btn btn-outline-success btn-sm" href="<?= $base ?>admin/login.php">Login Admin</a>
<?php endif; ?>
</div></div></nav>
<main class="container py-3">
<?php if ($flash): ?><div class="alert alert-<?= e($flash[1]) ?> mt-3"><?= e($flash[0]) ?></div><?php endif; ?>
