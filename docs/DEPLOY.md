# Panduan Deployment (InfinityFree) & GitHub

## 1. Jalankan lokal dulu (XAMPP)
1. Salin proyek ke `C:\xampp\htdocs\marketplace`.
2. Salin `config/koneksi.example.php` menjadi `config/koneksi.php` (default XAMPP: user `root`, password kosong).
3. Start Apache + MySQL, import `database/marketplace.sql` di phpMyAdmin.
4. Buka `http://localhost/marketplace/`. Login admin: `admin` / `admin123`.

## 2. Deploy ke InfinityFree (Soal 11)
1. Daftar di https://infinityfree.com, buat **Hosting Account** (pilih subdomain gratis).
2. Di Client Area → **MySQL Databases** → buat database. Catat *hostname* (mis. `sqlXXX.infinityfree.com`), *username*, *nama DB*, dan *password* akun hosting.
3. Buka **phpMyAdmin** dari panel, pilih database tadi, lalu **Import** `database/marketplace.sql`. **Hapus dulu dua baris** `CREATE DATABASE ...` dan `USE ...` di bagian atas file (InfinityFree tidak mengizinkan membuat DB lewat SQL).
4. Di komputer, buat `config/koneksi.php` dari contoh dan isi dengan data InfinityFree (blok yang dikomentari).
5. Upload isi proyek (kecuali folder `.git`, `docs`, `database` bila mau) ke folder **`htdocs`** lewat File Manager online atau FTP (FileZilla). `config/koneksi.php` ikut di-upload ke hosting, **tetapi tidak ke GitHub**.
6. Buka `https://namaanda.infinityfreeapp.com/` untuk uji. Segera **ganti password admin** (lihat bawah).

> Catatan: hosting gratis kadang memunculkan halaman peringatan/anti-bot pada request non-browser, sehingga API sebaiknya diuji di XAMPP (sesuai Soal 12).

### Ganti password admin
Buat file sementara `hash.php` berisi `<?php echo password_hash('PasswordBaruKuat', PASSWORD_DEFAULT);`, buka sekali di browser, salin hasilnya, lalu di phpMyAdmin jalankan:
`UPDATE admin SET password_hash='HASIL_HASH' WHERE username='admin';` — lalu **hapus** `hash.php`.

## 3. Upload ke GitHub
```bash
cd marketplace
git init
git add .
git status          # pastikan config/koneksi.php TIDAK ada di daftar
git commit -m "Marketplace tradisional PHP Native + REST API"
git branch -M main
git remote add origin https://github.com/USERNAME/marketplace-tradisional.git
git push -u origin main
```
`.gitignore` sudah mengecualikan `config/koneksi.php`. Jika password pernah ter-commit, ganti password database lalu hapus dari riwayat — jangan hanya menghapus file.

## 4. Isi laporan (Soal 11 bagian j)
- URL demo InfinityFree, URL repository GitHub
- Screenshot: daftar produk, detail, keranjang, checkout, pesanan sukses, panel admin (CRUD)
- Screenshot struktur database (phpMyAdmin → Designer/tab Struktur) dan hasil uji transaksi (isi tabel `pesanan` & `detail_pesanan`)
