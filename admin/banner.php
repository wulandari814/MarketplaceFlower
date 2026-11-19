<?php
require_once '../config/koneksi.php';
require_once '../includes/functions.php';
require_admin();

$conn->query("CREATE TABLE IF NOT EXISTS pengaturan_banner (
  id TINYINT UNSIGNED NOT NULL PRIMARY KEY,
  judul VARCHAR(160) NOT NULL DEFAULT 'Say it with beautiful flowers.',
  subjudul VARCHAR(255) NOT NULL DEFAULT 'Rangkaian bunga manis untuk setiap momen.',
  tombol VARCHAR(60) NOT NULL DEFAULT 'Temukan buketmu ♡',
  gambar VARCHAR(255) DEFAULT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$conn->query("INSERT IGNORE INTO pengaturan_banner (id) VALUES (1)");

$row = $conn->query("SELECT * FROM pengaturan_banner WHERE id=1")->fetch_assoc();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $judul = trim($_POST['judul'] ?? '');
    $subjudul = trim($_POST['subjudul'] ?? '');
    $tombol = trim($_POST['tombol'] ?? '');
    $gambar = $row['gambar'] ?? null;
    if ($judul === '' || $subjudul === '' || $tombol === '') $error = 'Judul, deskripsi, dan teks tombol wajib diisi.';
    if (!$error && !empty($_FILES['banner_file']['name'])) {
        $f = $_FILES['banner_file'];
        if ($f['error'] !== UPLOAD_ERR_OK) $error = 'Upload gambar gagal. Silakan coba lagi.';
        elseif ($f['size'] > 5 * 1024 * 1024) $error = 'Ukuran banner maksimal 5 MB.';
        else {
            $info = @getimagesize($f['tmp_name']);
            $mime = $info['mime'] ?? '';
            $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
            if (!$info || !isset($allowed[$mime])) $error = 'Format banner harus JPG, PNG, atau WEBP.';
            else {
                $dir = dirname(__DIR__) . '/uploads/banner/';
                if (!is_dir($dir)) @mkdir($dir, 0755, true);
                if (!is_dir($dir) || !is_writable($dir)) $error = 'Folder uploads/banner tidak bisa ditulis. Periksa permission hosting.';
                else {
                    $name = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
                    if (move_uploaded_file($f['tmp_name'], $dir . $name)) {
                        if ($gambar && strpos($gambar, 'uploads/banner/') === 0) { $old = dirname(__DIR__) . '/' . $gambar; if (is_file($old)) @unlink($old); }
                        $gambar = 'uploads/banner/' . $name;
                    } else $error = 'Banner tidak berhasil disimpan.';
                }
            }
        }
    }
    if (!$error) {
        $st = $conn->prepare("UPDATE pengaturan_banner SET judul=?, subjudul=?, tombol=?, gambar=? WHERE id=1");
        $st->bind_param('ssss', $judul, $subjudul, $tombol, $gambar);
        if ($st->execute()) { flash('Banner homepage berhasil diperbarui.'); redirect('banner.php'); }
        $error = 'Pengaturan banner gagal disimpan.';
    }
    $row['judul']=$judul; $row['subjudul']=$subjudul; $row['tombol']=$tombol; $row['gambar']=$gambar;
}
$base='../'; $judul='Pengaturan Banner'; include '../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
  <div><span class="eyebrow">Bloomé storefront</span><h3 class="mt-2 mb-0">Banner Homepage ♡</h3><p class="text-muted mb-0">Ganti foto dan tulisan utama toko tanpa mengedit kode.</p></div>
  <a class="btn btn-outline-success" href="produk.php">Kembali ke Admin</a>
</div>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data" class="shop-panel p-4">
  <?= csrf_field() ?>
  <div class="mb-3"><label class="form-label">Judul banner</label><input class="form-control" name="judul" maxlength="160" value="<?= e($row['judul']) ?>" required></div>
  <div class="mb-3"><label class="form-label">Deskripsi singkat</label><textarea class="form-control" name="subjudul" rows="2" maxlength="255" required><?= e($row['subjudul']) ?></textarea></div>
  <div class="mb-3"><label class="form-label">Tulisan tombol</label><input class="form-control" name="tombol" maxlength="60" value="<?= e($row['tombol']) ?>" required></div>
  <div class="mb-3"><label class="form-label">Upload foto banner</label><input class="form-control" type="file" name="banner_file" accept="image/jpeg,image/png,image/webp"><div class="form-text">Pilih foto dari perangkatmu. JPG, PNG, atau WEBP; maksimal 5 MB. Disarankan foto landscape 1600 × 700 px. Jika tidak memilih foto baru, foto lama tetap dipakai.</div></div>
  <?php if (!empty($row['gambar'])): ?><div class="mb-3"><div class="form-label">Banner saat ini</div><img src="../<?= e($row['gambar']) ?>" alt="Banner saat ini" style="display:block;width:100%;max-height:260px;object-fit:cover;border-radius:18px;border:1px solid #f3dfe6"></div><?php endif; ?>
  <button class="btn btn-success">Simpan Banner ♡</button>
  <a class="btn btn-light ms-2" href="../index.php" target="_blank">Lihat Toko</a>
</form>
<?php include '../includes/footer.php'; ?>
