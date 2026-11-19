<?php
require_once 'config/koneksi.php';
require_once 'includes/functions.php';
require_customer();
$cart = $_SESSION['cart'] ?? [];
if (!$cart) { flash('Keranjang kosong.', 'warning'); redirect('keranjang.php'); }

$error = ''; $v = ['nama' => '', 'email' => '', 'telepon' => '', 'alamat' => '', 'catatan' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    foreach ($v as $k => $_) $v[$k] = trim($_POST[$k] ?? '');
    if ($v['nama'] === '' || $v['alamat'] === '' || !filter_var($v['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Nama, email yang valid, dan alamat wajib diisi.';
    } else {
        $conn->begin_transaction();
        try {
            // 1. Data pembeli (email unik -> update bila sudah ada)
            $stmt = $conn->prepare("INSERT INTO pembeli (nama, email, telepon, alamat) VALUES (?,?,?,?)
                ON DUPLICATE KEY UPDATE nama=VALUES(nama), telepon=VALUES(telepon), alamat=VALUES(alamat), id_pembeli=LAST_INSERT_ID(id_pembeli)");
            $stmt->bind_param('ssss', $v['nama'], $v['email'], $v['telepon'], $v['alamat']);
            if (!$stmt->execute()) throw new Exception('Gagal menyimpan pembeli.');
            $id_pembeli = $conn->insert_id;

            // 2. Kunci baris produk, cek stok, hitung total (harga diambil dari DB)
            $baris = []; $total = 0;
            $cek = $conn->prepare("SELECT nama_produk, harga, stok FROM produk WHERE id_produk = ? FOR UPDATE");
            foreach ($cart as $id => $jml) {
                $id = (int)$id;
                $cek->bind_param('i', $id);
                $cek->execute();
                $p = $cek->get_result()->fetch_assoc();
                if (!$p) throw new Exception('Ada produk yang sudah tidak tersedia.');
                if ($p['stok'] < $jml) throw new Exception('Stok "' . $p['nama_produk'] . '" tidak mencukupi.');
                $sub = $p['harga'] * $jml;
                $total += $sub;
                $baris[] = [$id, $jml, $p['harga'], $sub];
            }

            // 3. Simpan pesanan
            $kode = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
            $stmt = $conn->prepare("INSERT INTO pesanan (kode_pesanan, id_pembeli, total, catatan) VALUES (?,?,?,?)");
            $stmt->bind_param('sids', $kode, $id_pembeli, $total, $v['catatan']);
            if (!$stmt->execute()) throw new Exception('Gagal menyimpan pesanan.');
            $id_pesanan = $conn->insert_id;

            // 4. Simpan detail + kurangi stok
            $det = $conn->prepare("INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, harga_satuan, subtotal) VALUES (?,?,?,?,?)");
            $stk = $conn->prepare("UPDATE produk SET stok = stok - ? WHERE id_produk = ?");
            foreach ($baris as [$idp, $jml, $hrg, $sub]) {
                $det->bind_param('iiidd', $id_pesanan, $idp, $jml, $hrg, $sub);
                if (!$det->execute()) throw new Exception('Gagal menyimpan detail pesanan.');
                $stk->bind_param('ii', $jml, $idp);
                $stk->execute();
            }
            $conn->commit();
            $_SESSION['cart'] = [];
            redirect('pesanan_sukses.php?kode=' . urlencode($kode));
        } catch (Exception $ex) {
            $conn->rollback();
            $error = $ex->getMessage();
        }
    }
}

$judul='Checkout';include 'includes/header.php';?>
<div class="page-title"><span class="eyebrow">Almost yours</span><h2 class="mt-3">Checkout pesanan ♡</h2><p>Isi detail pengiriman, lalu pesanan bungamu akan kami siapkan.</p></div>
<?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<div class="checkout-layout mb-5"><form method="post" class="checkout-card"><?= csrf_field() ?><h4 class="mb-4">Detail penerima</h4><div class="row g-3"><div class="col-12"><label class="form-label">Nama lengkap</label><input class="form-control" name="nama" value="<?= e($v['nama']) ?>" placeholder="Nama penerima" required></div><div class="col-md-6"><label class="form-label">Email</label><input type="email" class="form-control" name="email" value="<?= e($v['email']) ?>" placeholder="nama@email.com" required></div><div class="col-md-6"><label class="form-label">Nomor WhatsApp</label><input class="form-control" name="telepon" value="<?= e($v['telepon']) ?>" placeholder="08xxxxxxxxxx"></div><div class="col-12"><label class="form-label">Alamat pengiriman</label><textarea class="form-control" name="alamat" rows="3" placeholder="Nama jalan, kelurahan, kecamatan, kota" required><?= e($v['alamat']) ?></textarea></div><div class="col-12"><label class="form-label">Pesan pada kartu / catatan (opsional)</label><textarea class="form-control" name="catatan" rows="2" placeholder="Contoh: Happy birthday! Semoga harimu indah."><?= e($v['catatan']) ?></textarea></div></div><div class="d-flex flex-wrap gap-2 mt-4"><a href="keranjang.php" class="btn btn-light">Kembali</a><button class="btn btn-success flex-fill">Buat pesanan sekarang ♡</button></div></form>
<aside class="summary-card"><h4 class="mb-3">Pesananmu</h4><?php foreach($cart as $id=>$jml): $id=(int)$id;$st=$conn->prepare("SELECT nama_produk,harga FROM produk WHERE id_produk=?");$st->bind_param('i',$id);$st->execute();$it=$st->get_result()->fetch_assoc();if(!$it)continue; ?><div class="summary-line"><div><strong><?= e($it['nama_produk']) ?></strong><div class="small text-muted">Qty <?= (int)$jml ?></div></div><strong><?= rupiah($it['harga']*$jml) ?></strong></div><?php endforeach; ?><div class="summary-line mt-3"><strong>Total produk</strong><span class="summary-total"><?php $grand=0;foreach($cart as $id=>$jml){$id=(int)$id;$st=$conn->prepare("SELECT harga FROM produk WHERE id_produk=?");$st->bind_param('i',$id);$st->execute();$it=$st->get_result()->fetch_assoc();if($it)$grand+=$it['harga']*$jml;}echo rupiah($grand); ?></span></div><p class="small text-muted mt-3 mb-0">Biaya pengiriman akan dikonfirmasi oleh penjual setelah pesanan diterima.</p></aside></div>
<?php include 'includes/footer.php'; ?>
