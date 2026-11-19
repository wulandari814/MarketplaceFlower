# Bloomé Flower Shop

Website toko bunga berbasis PHP + MySQL dengan katalog produk, detail bunga, keranjang, checkout, kode pesanan, serta panel admin.

## Instalasi lokal / hosting
1. Buat database MySQL.
2. Import `database/marketplace.sql` ke database yang dipilih. Untuk InfinityFree, pilih database terlebih dahulu di phpMyAdmin dan hapus perintah `CREATE DATABASE` serta `USE db_marketplace` dari SQL sebelum import.
3. Isi `config/koneksi.php` dengan hostname, username, password, dan nama database dari panel hosting.
4. Upload seluruh isi folder `marketplace-umb` ke `htdocs` (InfinityFree).
5. Buka domain website.

## Akun admin awal
Username: `admin`
Password awal: `admin123`
Segera ganti password setelah instalasi.

## Catatan
- Produk contoh pada SQL sudah diubah menjadi katalog bunga.
- Foto contoh memakai URL gambar Unsplash; koneksi internet diperlukan untuk menampilkannya.
- Checkout menyimpan pesanan dan mengurangi stok. Pembayaran dan ongkos kirim belum terintegrasi otomatis; konfirmasi dilakukan oleh penjual.
- Sebelum website dipublikasikan, ganti password admin dan kredensial database contoh.


## Upload foto produk dari Admin
Login admin, buka Kelola Produk > Tambah Produk. Pilih file foto dari perangkat pada bagian Foto bunga, lalu Simpan. Format yang didukung JPG, PNG, dan WEBP dengan ukuran maksimal 3 MB. Foto disimpan ke `uploads/produk/`; folder ini harus memiliki izin tulis pada hosting. Saat mengedit produk, foto lama tetap dipakai jika tidak memilih file baru.

Produk contoh pada SQL tidak lagi memakai URL gambar eksternal. Setelah import, unggah foto masing-masing produk melalui halaman admin.


## Mengatur banner homepage
Login admin, buka menu Kelola Produk, lalu pilih tombol **Banner Homepage**. Kamu dapat mengunggah foto JPG/PNG/WEBP maksimal 5 MB dan mengubah judul, deskripsi, serta teks tombol. Tabel pengaturan banner dibuat otomatis oleh aplikasi sehingga tidak perlu import ulang database.
