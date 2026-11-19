<?php
require_once '../config/koneksi.php';
require_once '../includes/functions.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$p = ['id_kategori' => '', 'id_penjual' => '', 'nama_produk' => '', 'deskripsi' => '', 'harga' => '', 'stok' => 0, 'gambar' => ''];
if ($id) {
    $stmt = $conn->prepare("SELECT * FROM produk WHERE id_produk = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if (!$row) { flash('Produk tidak ditemukan.', 'danger'); redirect('produk.php'); }
    $p = $row;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $p['id_kategori'] = (int)($_POST['id_kategori'] ?? 0);
    $p['id_penjual']  = (int)($_POST['id_penjual'] ?? 0);
    $p['nama_produk'] = trim($_POST['nama_produk'] ?? '');
    $p['deskripsi']   = trim($_POST['deskripsi'] ?? '');
    $p['harga']       = (float)($_POST['harga'] ?? 0);
    $p['stok']        = (int)($_POST['stok'] ?? 0);
    // Gambar lama tetap dipakai jika admin tidak memilih file baru.
    $gambar_lama = $p['gambar'];
    if ($p['nama_produk'] === '' || $p['harga'] <= 0 || $p['stok'] < 0 || !$p['id_kategori'] || !$p['id_penjual']) $error = 'Lengkapi data dengan benar (harga > 0, stok >= 0).';
    else {
        $g = $gambar_lama ?: null;
        if (!empty($_FILES['gambar_file']['name'])) {
            $file = $_FILES['gambar_file'];
            if ($file['error'] !== UPLOAD_ERR_OK) $error = 'Upload gambar gagal. Coba pilih file kembali.';
            elseif ($file['size'] > 3 * 1024 * 1024) $error = 'Ukuran gambar maksimal 3 MB.';
            else {
                $info = @getimagesize($file['tmp_name']);
                $mime = $info['mime'] ?? '';
                $allowed = ['image/jpeg'=>'jpg', 'image/png'=>'png', 'image/webp'=>'webp'];
                if (!$info || !isset($allowed[$mime])) $error = 'Format gambar harus JPG, PNG, atau WEBP.';
                else {
                    $uploadDir = dirname(__DIR__) . '/uploads/produk/';
                    if (!is_dir($uploadDir)) @mkdir($uploadDir, 0755, true);
                    if (!is_dir($uploadDir) || !is_writable($uploadDir)) $error = 'Folder uploads/produk tidak dapat ditulis. Periksa permission hosting.';
                    else {
                        $namaFile = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
                        if (move_uploaded_file($file['tmp_name'], $uploadDir . $namaFile)) {
                            $g = 'uploads/produk/' . $namaFile;
                            if ($gambar_lama && strpos($gambar_lama, 'uploads/produk/') === 0) {
                                $oldPath = dirname(__DIR__) . '/' . $gambar_lama;
                                if (is_file($oldPath)) @unlink($oldPath);
                            }
                        } else $error = 'Gambar tidak berhasil disimpan ke server.';
                    }
                }
            }
        }
        if (!$error && $id) {
            $stmt = $conn->prepare("UPDATE produk SET id_kategori=?, id_penjual=?, nama_produk=?, deskripsi=?, harga=?, stok=?, gambar=? WHERE id_produk=?");
            $stmt->bind_param('iissdisi', $p['id_kategori'], $p['id_penjual'], $p['nama_produk'], $p['deskripsi'], $p['harga'], $p['stok'], $g, $id);
        } elseif (!$error) {
            $stmt = $conn->prepare("INSERT INTO produk (id_kategori, id_penjual, nama_produk, deskripsi, harga, stok, gambar) VALUES (?,?,?,?,?,?,?)");
            $stmt->bind_param('iissdis', $p['id_kategori'], $p['id_penjual'], $p['nama_produk'], $p['deskripsi'], $p['harga'], $p['stok'], $g);
        }
        if (!$error && $stmt->execute()) { flash($id ? 'Produk diperbarui.' : 'Produk ditambahkan.'); redirect('produk.php'); }
        if (!$error) $error = 'Gagal menyimpan produk.';
    }
}
$kat = $conn->query("SELECT * FROM kategori ORDER BY nama_kategori");
$pen = $conn->query("SELECT * FROM penjual ORDER BY nama_toko");
$base = '../'; $judul = $id ? 'Edit Produk' : 'Tambah Produk';
include '../includes/header.php';
?>
<h3><?= $id ? 'Edit' : 'Tambah' ?> Produk</h3>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data" class="row g-3 bg-white p-4 rounded shadow-sm">
  <?= csrf_field() ?>
  <div class="col-md-12"><label class="form-label">Nama produk</label><input class="form-control" name="nama_produk" value="<?= e($p['nama_produk']) ?>" required></div>
  <div class="col-md-6"><label class="form-label">Kategori</label>
    <select class="form-select" name="id_kategori" required><option value="">- pilih -</option>
      <?php while ($k = $kat->fetch_assoc()): ?><option value="<?= $k['id_kategori'] ?>" <?= $p['id_kategori'] == $k['id_kategori'] ? 'selected' : '' ?>><?= e($k['nama_kategori']) ?></option><?php endwhile; ?>
    </select></div>
  <div class="col-md-6"><label class="form-label">Penjual</label>
    <select class="form-select" name="id_penjual" required><option value="">- pilih -</option>
      <?php while ($s = $pen->fetch_assoc()): ?><option value="<?= $s['id_penjual'] ?>" <?= $p['id_penjual'] == $s['id_penjual'] ? 'selected' : '' ?>><?= e($s['nama_toko']) ?></option><?php endwhile; ?>
    </select></div>
  <div class="col-md-6"><label class="form-label">Harga (Rp)</label><input type="number" step="100" min="1" class="form-control" name="harga" value="<?= e($p['harga']) ?>" required></div>
  <div class="col-md-6"><label class="form-label">Stok</label><input type="number" min="0" class="form-control" name="stok" value="<?= e($p['stok']) ?>" required></div>
  <div class="col-12"><label class="form-label">Foto bunga</label><input class="form-control" type="file" name="gambar_file" accept="image/jpeg,image/png,image/webp"><div class="form-text">Pilih foto dari komputer atau HP (JPG, PNG, WEBP; maksimal 3 MB). Jika edit produk dan tidak memilih foto baru, foto lama tetap digunakan.</div><?php if (!empty($p['gambar'])): ?><div class="mt-3"><img src="../<?= e(gambar_src($p['gambar'])) ?>" alt="Foto produk saat ini" style="width:130px;height:130px;object-fit:cover;border-radius:18px;border:1px solid #f3dfe6"></div><?php endif; ?></div>
  <div class="col-12"><label class="form-label">Deskripsi</label><textarea class="form-control" name="deskripsi" rows="4"><?= e($p['deskripsi']) ?></textarea></div>
  <div class="col-12 d-flex gap-2"><a href="produk.php" class="btn btn-outline-secondary">Batal</a><button class="btn btn-success">Simpan</button></div>
</form>
<?php include '../includes/footer.php'; ?>
