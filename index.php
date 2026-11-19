<?php
require_once 'config/koneksi.php';
$judul = 'Fresh flowers, happy moments';
$q = trim($_GET['q'] ?? '');
$kat = (int)($_GET['kategori'] ?? 0);
$sql = "SELECT p.*, k.nama_kategori, s.nama_toko FROM produk p JOIN kategori k ON k.id_kategori=p.id_kategori JOIN penjual s ON s.id_penjual=p.id_penjual WHERE 1=1";
$types=''; $params=[];
if ($q !== '') { $sql .= " AND p.nama_produk LIKE ?"; $types.='s'; $params[]="%$q%"; }
if ($kat > 0) { $sql .= " AND p.id_kategori = ?"; $types.='i'; $params[]=$kat; }
$sql .= " ORDER BY p.created_at DESC, p.id_produk DESC";
$stmt=$conn->prepare($sql); if ($params) $stmt->bind_param($types,...$params); $stmt->execute(); $produk=$stmt->get_result();
$banner = ['judul'=>'Say it with beautiful flowers.', 'subjudul'=>'Rangkaian bunga manis untuk merayakan hari spesial, mengirim rasa sayang, atau sekadar membuat hari jadi lebih indah.', 'tombol'=>'Temukan buketmu ♡', 'gambar'=>null];
// Tabel dibuat otomatis agar pembaruan banner tidak mengharuskan import ulang database.
$conn->query("CREATE TABLE IF NOT EXISTS pengaturan_banner (id TINYINT UNSIGNED NOT NULL PRIMARY KEY, judul VARCHAR(160) NOT NULL DEFAULT 'Say it with beautiful flowers.', subjudul VARCHAR(255) NOT NULL DEFAULT 'Rangkaian bunga manis untuk setiap momen.', tombol VARCHAR(60) NOT NULL DEFAULT 'Temukan buketmu ♡', gambar VARCHAR(255) DEFAULT NULL, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$conn->query("INSERT IGNORE INTO pengaturan_banner (id) VALUES (1)");
$bannerResult = $conn->query("SELECT judul, subjudul, tombol, gambar FROM pengaturan_banner WHERE id=1");
if ($bannerResult && $bannerResult->num_rows) $banner = array_merge($banner, $bannerResult->fetch_assoc());
$kategori=$conn->query("SELECT * FROM kategori ORDER BY nama_kategori");
$kategori_list=[]; while($k=$kategori->fetch_assoc()) $kategori_list[]=$k;
include 'includes/header.php';
?>
<section class="hero <?= !empty($banner['gambar']) ? 'hero-has-banner' : '' ?>" <?php if (!empty($banner['gambar'])): ?>style="background-image:linear-gradient(90deg,rgba(255,242,247,.96) 0%,rgba(255,242,247,.86) 48%,rgba(255,242,247,.12) 100%),url('<?= e($banner['gambar']) ?>');background-size:cover;background-position:center"<?php endif; ?>><div class="hero-copy"><span class="eyebrow">✿ A little joy, in every petal</span><h1><?= e($banner['judul']) ?></h1><p><?= e($banner['subjudul']) ?></p><a href="#koleksi" class="btn btn-success btn-lg"><?= e($banner['tombol']) ?></a></div><?php if (empty($banner['gambar'])): ?><img class="hero-art" src="assets/img/flower-placeholder.svg" alt="Bouquet bunga pink"><?php endif; ?></section>
<div class="section-heading" id="koleksi"><div><span class="eyebrow">The flower corner</span><h2 class="mt-3 mb-1">Temukan bunga favoritmu</h2><p>Dipilih dengan hati, dirangkai dengan cinta.</p></div><span class="product-meta">✿ Fresh blooms everyday</span></div>
<div class="category-pills"><a class="<?= $kat===0?'active':'' ?>" href="index.php">Semua bunga</a><?php foreach($kategori_list as $k): ?><a class="<?= $kat==(int)$k['id_kategori']?'active':'' ?>" href="?kategori=<?= (int)$k['id_kategori'] ?>"><?= e($k['nama_kategori']) ?></a><?php endforeach; ?></div>
<form class="search-panel row g-2" method="get"><input type="hidden" name="kategori" value="<?= $kat ?>"><div class="col-md-9"><input class="form-control" name="q" placeholder="Cari mawar, tulip, lily, dan lainnya..." value="<?= e($q) ?>"></div><div class="col-md-3"><button class="btn btn-success w-100">Cari bunga ♡</button></div></form>
<div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
<?php while($p=$produk->fetch_assoc()): ?><div class="col"><article class="card h-100"><img src="<?= e(gambar_src($p['gambar'])) ?>" class="card-img-top" alt="<?= e($p['nama_produk']) ?>"><div class="card-body"><span class="badge"><?= e($p['nama_kategori']) ?></span><h5 class="card-title mt-3 mb-1"><?= e($p['nama_produk']) ?></h5><div class="product-meta mb-3">♡ <?= e($p['nama_toko']) ?> · Stok <?= (int)$p['stok'] ?></div><div class="product-price"><?= rupiah($p['harga']) ?></div></div><div class="card-footer border-0 d-flex gap-2"><a class="btn btn-outline-success flex-fill" href="produk_detail.php?id=<?= (int)$p['id_produk'] ?>">Lihat detail</a><?php if(is_admin()): ?><span class="small text-muted align-self-center">Mode admin</span><?php elseif($p['stok']>0): ?><form method="post" action="keranjang.php" class="flex-fill"><?= csrf_field() ?><input type="hidden" name="aksi" value="tambah"><input type="hidden" name="id" value="<?= (int)$p['id_produk'] ?>"><button class="btn btn-success w-100">+ Keranjang</button></form><?php else: ?><button class="btn btn-light flex-fill" disabled>Habis</button><?php endif; ?></div></article></div><?php endwhile; ?>
<?php if($produk->num_rows===0): ?><div class="col-12"><div class="shop-panel p-5 text-center"><div style="font-size:42px">🌷</div><h3>Belum menemukan bunganya?</h3><p class="text-muted mb-0">Coba kata kunci atau kategori yang lain, ya.</p></div></div><?php endif; ?></div>
<?php include 'includes/footer.php'; ?>
