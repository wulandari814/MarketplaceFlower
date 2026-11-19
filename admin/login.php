<?php
require_once '../config/koneksi.php';
require_once '../includes/functions.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $stmt = $conn->prepare("SELECT id_admin, password_hash FROM admin WHERE username = ?");
    $u = trim($_POST['username'] ?? '');
    $stmt->bind_param('s', $u);
    $stmt->execute();
    $a = $stmt->get_result()->fetch_assoc();
    if ($a && password_verify($_POST['password'] ?? '', $a['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $a['id_admin'];
        unset($_SESSION['cart']); // Pisahkan sesi admin dari keranjang pembeli.
        redirect('produk.php');
    }
    $error = 'Username atau password salah.';
}
$base = '../'; $judul = 'Login Admin';
include '../includes/header.php';
?>
<div class="row justify-content-center"><div class="col-md-4">
  <div class="card shadow-sm"><div class="card-body">
    <h4 class="mb-3">Login Admin</h4>
    <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
    <form method="post"><?= csrf_field() ?>
      <div class="mb-3"><label class="form-label">Username</label><input class="form-control" name="username" required></div>
      <div class="mb-3"><label class="form-label">Password</label><input type="password" class="form-control" name="password" required></div>
      <button class="btn btn-success w-100">Masuk</button>
    </form>
  </div></div>
</div></div>
<?php include '../includes/footer.php'; ?>
