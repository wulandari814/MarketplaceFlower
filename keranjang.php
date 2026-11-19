<?php
require_once 'config/koneksi.php';
require_once 'includes/functions.php';
require_customer();
$cart = &$_SESSION['cart'];
if (!is_array($cart)) $cart = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $aksi = $_POST['aksi'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($aksi === 'tambah' || $aksi === 'ubah') {
        $jumlah = max(1, (int)($_POST['jumlah'] ?? 1));
        $stmt = $conn->prepare("SELECT stok FROM produk WHERE id_produk = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if (!$row) { flash('Produk tidak ditemukan.', 'danger'); redirect('keranjang.php'); }
        $baru = ($aksi === 'tambah') ? ($cart[$id] ?? 0) + $jumlah : $jumlah;
        if ($baru > $row['stok']) { $baru = (int)$row['stok']; flash('Jumlah disesuaikan dengan stok tersedia.', 'warning'); }
        else flash($aksi === 'tambah' ? 'Produk ditambahkan ke keranjang.' : 'Keranjang diperbarui.');
        if ($baru > 0) $cart[$id] = $baru; else unset($cart[$id]);
    } elseif ($aksi === 'hapus') {
        unset($cart[$id]);
        flash('Produk dihapus dari keranjang.');
    } elseif ($aksi === 'kosongkan') {
        $cart = [];
    }
    redirect($aksi === 'tambah' ? ($_SERVER['HTTP_REFERER'] ?? 'keranjang.php') : 'keranjang.php');
}

$items = []; $total = 0;
if ($cart) {
    $ids = array_map('intval', array_keys($cart));
    $in = implode(',', $ids); // aman: sudah dipaksa integer
    $res = $conn->query("SELECT id_produk, nama_produk, harga, stok, gambar FROM produk WHERE id_produk IN ($in)");
    while ($r = $res->fetch_assoc()) {
        $r['jumlah'] = $cart[$r['id_produk']];
        $r['subtotal'] = $r['jumlah'] * $r['harga'];
        $total += $r['subtotal'];
        $items[] = $r;
    }
}
$judul = 'Your flower bag';
include 'includes/header.php';
?>
<div class="page-title"><span class="eyebrow">A little happiness is on its way</span><h2 class="mt-3">Keranjang belanja ♡</h2><p>Periksa bunga pilihanmu sebelum melanjutkan.</p></div>
<?php if(!$items): ?><div class="shop-panel p-5 text-center mb-5"><div style="font-size:54px">💐</div><h3>Keranjangmu masih kosong</h3><p class="text-muted">Yuk, pilih buket cantik untuk orang tersayang.</p><a href="index.php" class="btn btn-success">Mulai belanja</a></div><?php else: ?>
<div class="table-responsive mb-4"><table class="table align-middle mb-0"><thead><tr><th>Produk</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th><th></th></tr></thead><tbody><?php foreach($items as $it): ?><tr><td><div class="d-flex align-items-center gap-3"><img src="<?= e(gambar_src($it['gambar'])) ?>" alt="" style="width:76px;height:76px;object-fit:cover;border-radius:15px"><strong><?= e($it['nama_produk']) ?></strong></div></td><td><?= rupiah($it['harga']) ?></td><td><form method="post" class="d-flex gap-1"><?= csrf_field() ?><input type="hidden" name="aksi" value="ubah"><input type="hidden" name="id" value="<?= (int)$it['id_produk'] ?>"><input type="number" name="jumlah" class="form-control form-control-sm" style="width:75px" value="<?= (int)$it['jumlah'] ?>" min="1" max="<?= (int)$it['stok'] ?>"><button class="btn btn-sm btn-outline-success">OK</button></form></td><td class="fw-bold"><?= rupiah($it['subtotal']) ?></td><td><form method="post"><?= csrf_field() ?><input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?= (int)$it['id_produk'] ?>"><button class="btn btn-sm btn-outline-danger">Hapus</button></form></td></tr><?php endforeach; ?></tbody></table></div>
<div class="row justify-content-end mb-5"><div class="col-md-5 col-lg-4"><div class="summary-card"><div class="d-flex justify-content-between mb-3"><span>Subtotal</span><strong><?= rupiah($total) ?></strong></div><div class="d-flex justify-content-between mb-4"><span>Pengiriman</span><span class="text-muted">Dikonfirmasi penjual</span></div><div class="summary-line"><strong>Total sementara</strong><span class="summary-total"><?= rupiah($total) ?></span></div><a href="checkout.php" class="btn btn-success w-100 mt-4">Lanjut checkout ♡</a><a href="index.php" class="btn btn-light w-100 mt-2">Lanjut belanja</a></div></div></div><?php endif; ?>
<?php include 'includes/footer.php'; ?>
