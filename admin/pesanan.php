<?php
require_once '../config/koneksi.php';
require_once '../includes/functions.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $allowed = ['baru','diproses','dikirim','selesai','batal'];
    $st = $_POST['status'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    if (in_array($st, $allowed, true)) {
        $stmt = $conn->prepare("UPDATE pesanan SET status=? WHERE id_pesanan=?");
        $stmt->bind_param('si', $st, $id);
        $stmt->execute();
        flash('Status pesanan diperbarui.');
    }
    redirect('pesanan.php');
}
$res = $conn->query("SELECT ps.*, pb.nama FROM pesanan ps JOIN pembeli pb ON pb.id_pembeli=ps.id_pembeli ORDER BY ps.id_pesanan DESC");
$base = '../'; $judul = 'Data Pesanan';
include '../includes/header.php';
?>
<div class="d-flex justify-content-between mb-3"><h3>Data Pesanan</h3><a class="btn btn-outline-secondary" href="produk.php">&larr; Produk</a></div>
<div class="table-responsive">
<table class="table table-striped align-middle bg-white shadow-sm">
  <thead class="table-success"><tr><th>Kode</th><th>Tanggal</th><th>Pembeli</th><th>Total</th><th>Status</th></tr></thead>
  <tbody>
  <?php while ($r = $res->fetch_assoc()): ?>
    <tr><td><?= e($r['kode_pesanan']) ?></td><td><?= e($r['tanggal']) ?></td><td><?= e($r['nama']) ?></td><td><?= rupiah($r['total']) ?></td>
      <td><form method="post" class="d-flex gap-1"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $r['id_pesanan'] ?>">
        <select name="status" class="form-select form-select-sm">
          <?php foreach (['baru','diproses','dikirim','selesai','batal'] as $s): ?><option <?= $r['status']===$s?'selected':'' ?>><?= $s ?></option><?php endforeach; ?>
        </select><button class="btn btn-sm btn-outline-success">Simpan</button></form></td></tr>
  <?php endwhile; ?>
  </tbody>
</table>
</div>
<?php include '../includes/footer.php'; ?>
