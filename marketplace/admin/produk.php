<?php
require_once '../config/koneksi.php';
require_once '../includes/functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {   // DELETE
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    $stmt = $conn->prepare("DELETE FROM produk WHERE id_produk = ?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) flash('Produk dihapus.');
    else flash('Produk tidak bisa dihapus karena sudah ada di pesanan.', 'danger');
    redirect('produk.php');
}
$res = $conn->query("SELECT p.*, k.nama_kategori, s.nama_toko FROM produk p
    JOIN kategori k ON k.id_kategori=p.id_kategori JOIN penjual s ON s.id_penjual=p.id_penjual ORDER BY p.id_produk DESC");
$base = '../'; $judul = 'Kelola Produk';
include '../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Kelola Produk</h3>
  <div class="d-flex gap-2">
    <a class="btn btn-success" href="produk_form.php">+ Tambah Produk</a>
    <a class="btn btn-outline-secondary" href="banner.php">♡ Banner Homepage</a>
    <a class="btn btn-outline-secondary" href="pesanan.php">Data Pesanan</a>
    <a class="btn btn-outline-danger" href="logout.php">Logout</a>
  </div>
</div>
<div class="table-responsive">
<table class="table table-striped align-middle bg-white shadow-sm">
  <thead class="table-success"><tr><th>#</th><th>Produk</th><th>Kategori</th><th>Penjual</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr></thead>
  <tbody>
  <?php while ($p = $res->fetch_assoc()): ?>
    <tr>
      <td><?= $p['id_produk'] ?></td><td><?= e($p['nama_produk']) ?></td><td><?= e($p['nama_kategori']) ?></td>
      <td><?= e($p['nama_toko']) ?></td><td><?= rupiah($p['harga']) ?></td><td><?= $p['stok'] ?></td>
      <td class="d-flex gap-1">
        <a class="btn btn-sm btn-outline-primary" href="produk_form.php?id=<?= $p['id_produk'] ?>">Edit</a>
        <form method="post" onsubmit="return confirm('Hapus produk ini?')"><?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= $p['id_produk'] ?>"><button class="btn btn-sm btn-outline-danger">Hapus</button></form>
      </td>
    </tr>
  <?php endwhile; ?>
  </tbody>
</table>
</div>
<?php include '../includes/footer.php'; ?>
