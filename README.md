# 🌸 Bloomé Flower Shop – Website Marketplace

## 1. Deskripsi Proyek

Bloomé Flower Shop adalah aplikasi website toko bunga berbasis **PHP Native** dan **MySQL**. Proyek ini dikembangkan untuk memenuhi **Tugas 11 – Pengembangan Web**. Website menyediakan katalog bunga yang dapat dilihat oleh pengunjung, fitur keranjang belanja, proses checkout, serta halaman administrator untuk mengelola produk, banner homepage, dan pesanan.

Sistem memiliki dua konteks penggunaan, yaitu pengunjung/pembeli dan administrator. Pembeli dapat memilih produk dan membuat pesanan tanpa login pelanggan, sedangkan admin harus login untuk mengakses halaman pengelolaan. Admin tidak dapat melakukan checkout selama sedang login sebagai admin.

## 2. Fitur Website

### Fitur untuk Pengunjung/Pembeli
- Melihat katalog produk bunga.
- Mencari produk berdasarkan nama.
- Memfilter produk berdasarkan kategori.
- Melihat detail produk, deskripsi, harga, stok, dan nama toko.
- Menambahkan produk ke keranjang.
- Mengubah jumlah produk atau menghapus produk dari keranjang.
- Melakukan checkout dengan mengisi nama, email, nomor WhatsApp, alamat, dan catatan opsional.
- Mendapatkan kode pesanan dan melihat ringkasan pesanan setelah checkout.

### Fitur Administrator
- Login dan logout administrator.
- Melihat daftar produk.
- Menambahkan produk baru.
- Mengubah data produk.
- Menghapus produk jika produk belum digunakan dalam pesanan.
- Mengunggah foto produk dari perangkat.
- Mengubah judul, deskripsi, teks tombol, dan foto banner homepage.
- Melihat daftar pesanan pelanggan.
- Mengubah status pesanan menjadi `baru`, `diproses`, `dikirim`, `selesai`, atau `batal`.

**Pembatasan Hak Akses:** Halaman pengelolaan dilindungi dengan pemeriksaan sesi admin. Ketika admin sedang login, fitur keranjang dan checkout untuk pembeli dibatasi. Admin perlu logout untuk kembali menggunakan website sebagai pembeli.

## 3. Teknologi yang Digunakan

| Teknologi | Fungsi |
|---|---|
| PHP Native | Memproses logika aplikasi di sisi server |
| MySQL | Menyimpan data aplikasi |
| MySQLi | Menghubungkan PHP dengan database MySQL |
| HTML | Membentuk struktur halaman |
| CSS | Mengatur tampilan website |
| JavaScript | Mendukung interaksi antarmuka |
| Bootstrap 5.3.3 | Komponen dan tata letak antarmuka |
| XAMPP | Menjalankan Apache dan MySQL secara lokal |
| Visual Studio Code | Editor kode program |

## 4. Struktur Folder dan File

```text
marketplace/
├── .gitignore
├── README.md
├── index.php
├── produk_detail.php
├── keranjang.php
├── checkout.php
├── pesanan_sukses.php
│
├── admin/
│   ├── login.php
│   ├── logout.php
│   ├── produk.php
│   ├── produk_form.php
│   ├── banner.php
│   └── pesanan.php
│
├── config/
│   └── koneksi.example.php
│
├── database/
│   └── marketplace.sql
│
├── docs/
│   ├── DEPLOY.md
│   └── PERANCANGAN.md
│
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── functions.php
│
├── assets/
│   ├── style.css
│   └── img/
│       └── flower-placeholder.svg
│
└── uploads/
    ├── banner/
    └── produk/
```

### Penjelasan File Utama

| File/Folder | Fungsi |
|---|---|
| `index.php` | Halaman utama yang menampilkan banner, katalog, pencarian, dan filter kategori. |
| `produk_detail.php` | Menampilkan detail produk yang dipilih. |
| `keranjang.php` | Mengelola isi keranjang, jumlah barang, dan subtotal. |
| `checkout.php` | Memvalidasi data pembeli, memeriksa stok, menghitung total, dan menyimpan pesanan. |
| `pesanan_sukses.php` | Menampilkan kode dan ringkasan pesanan yang berhasil dibuat. |
| `admin/login.php` | Memeriksa username dan password administrator. |
| `admin/logout.php` | Mengakhiri sesi administrator. |
| `admin/produk.php` | Menampilkan dan menghapus produk serta menyediakan navigasi pengelolaan. |
| `admin/produk_form.php` | Menambahkan dan mengubah informasi produk serta mengunggah foto produk. |
| `admin/banner.php` | Mengubah konten dan foto banner homepage. |
| `admin/pesanan.php` | Menampilkan pesanan dan memperbarui statusnya. |
| `config/koneksi.example.php` | Contoh konfigurasi koneksi database. Salin menjadi `koneksi.php` untuk penggunaan lokal. |
| `database/marketplace.sql` | Membuat tabel utama dan memasukkan data awal aplikasi. |
| `includes/functions.php` | Berisi fungsi bantuan, pengelolaan sesi, notifikasi, token CSRF, dan pemeriksaan hak akses. |
| `includes/header.php` | Menyediakan bagian kepala halaman dan navigasi website. |
| `includes/footer.php` | Menyediakan bagian footer website. |
| `assets/style.css` | Mengatur desain dan tampilan website. |
| `uploads/produk/` | Menyimpan foto produk yang diunggah melalui halaman admin. |
| `uploads/banner/` | Menyimpan foto banner homepage yang diunggah melalui halaman admin. |

## 5. Cara Kerja Website

Website dijalankan melalui browser dengan bantuan server Apache dari XAMPP. Saat pengguna membuka halaman, Apache meneruskan permintaan ke file PHP yang sesuai. File PHP menjalankan logika aplikasi dan, jika membutuhkan data, menggunakan MySQLi untuk mengakses database MySQL. Hasil pemrosesan kemudian ditampilkan kepada pengguna dalam bentuk halaman website.

Alur sederhananya:

1. Pengguna membuka alamat website melalui browser.
2. Apache menerima permintaan halaman.
3. PHP menjalankan file yang diminta.
4. PHP menggunakan MySQLi untuk membaca atau mengubah data pada database jika diperlukan.
5. MySQL mengirimkan hasil query kepada PHP.
6. PHP menghasilkan halaman HTML.
7. Browser menampilkan halaman kepada pengguna.

## 6. Alur Login Administrator

1. Admin membuka halaman `http://localhost/marketplace/admin/login.php`.
2. Admin memasukkan username dan password.
3. Sistem mencari username pada tabel `admin`.
4. Sistem memverifikasi password menggunakan `password_verify()` terhadap password hash yang tersimpan di database.
5. Jika cocok, sistem membuat sesi administrator dan mengarahkan admin ke halaman pengelolaan produk.
6. Jika tidak cocok, sistem menampilkan pesan bahwa username atau password salah.
7. Admin dapat keluar melalui tombol Logout.

### Akun Administrator Awal

| Keterangan | Informasi |
|---|---|
| Halaman Login | `http://localhost/marketplace/admin/login.php` |
| Username | `admin` |
| Password Awal | `admin123` |
| Halaman Setelah Login | `admin/produk.php` |

Akun tersebut merupakan akun awal yang dimasukkan oleh file SQL. Untuk website yang dipublikasikan, segera ganti password awal dengan password yang kuat. Hindari membagikan kredensial admin pada repository publik.

## 7. Alur Pengelolaan Produk oleh Admin

1. Admin login melalui halaman login administrator.
2. Sistem mengarahkan admin ke halaman Kelola Produk.
3. Admin dapat memilih Tambah Produk untuk memasukkan nama produk, kategori, penjual, deskripsi, harga, stok, dan foto.
4. Admin dapat memilih Edit untuk memperbarui data produk.
5. Admin dapat memilih Hapus untuk menghapus produk yang belum terhubung dengan transaksi pesanan.
6. Data yang berhasil disimpan akan diperbarui pada database dan katalog website.

Foto produk dapat diunggah dari perangkat dalam format JPG, PNG, atau WEBP dengan ukuran maksimal 3 MB.

## 8. Alur Pengaturan Banner Homepage

1. Admin login.
2. Admin membuka menu **Banner Homepage** dari halaman Kelola Produk.
3. Admin mengubah judul, deskripsi singkat, dan tulisan tombol.
4. Admin dapat memilih foto banner baru dari perangkat.
5. Admin menekan tombol Simpan Banner.
6. Website menyimpan pengaturan dan menampilkan banner terbaru pada halaman utama.

Foto banner mendukung format JPG, PNG, dan WEBP dengan ukuran maksimal 5 MB. Tabel `pengaturan_banner` dibuat otomatis oleh aplikasi saat halaman utama atau halaman pengaturan banner dijalankan.

## 9. Alur Pemesanan oleh Pembeli

Pembeli tidak perlu login sebagai administrator untuk melakukan pemesanan. Prosesnya adalah sebagai berikut:

1. Pembeli membuka halaman utama Bloomé Flower Shop.
2. Pembeli mencari atau memilih kategori bunga.
3. Pembeli membuka detail produk untuk melihat deskripsi, harga, dan stok.
4. Pembeli menambahkan produk ke keranjang.
5. Pembeli membuka keranjang untuk mengubah jumlah atau menghapus produk.
6. Pembeli memilih Checkout.
7. Pembeli mengisi nama, email yang valid, nomor WhatsApp, alamat, dan catatan jika diperlukan.
8. Sistem memeriksa ketersediaan stok dan menghitung total berdasarkan harga produk di database.
9. Jika data valid dan stok mencukupi, sistem menyimpan data pembeli, pesanan, dan detail pesanan.
10. Sistem mengurangi stok produk, mengosongkan keranjang, lalu menampilkan kode pesanan dan ringkasan pesanan.

Proses penyimpanan checkout menggunakan transaksi database. Jika terjadi kegagalan pada proses penyimpanan, sistem membatalkan transaksi agar perubahan data tidak tersimpan sebagian.

**Catatan:** Website mencatat pesanan dan statusnya. Pembayaran dan ongkos kirim belum terintegrasi secara otomatis; konfirmasi pengiriman atau biaya dilakukan oleh penjual.

## 10. Database

Nama database yang digunakan pada konfigurasi contoh adalah `db_marketplace`.

| Tabel | Kegunaan |
|---|---|
| `admin` | Menyimpan username dan password hash administrator. |
| `kategori` | Menyimpan kategori bunga. |
| `penjual` | Menyimpan informasi toko atau penjual. |
| `produk` | Menyimpan nama, kategori, penjual, deskripsi, harga, stok, dan lokasi foto produk. |
| `pembeli` | Menyimpan data pembeli yang diisi saat checkout. |
| `pesanan` | Menyimpan kode, tanggal, pembeli, total, status, dan catatan pesanan. |
| `detail_pesanan` | Menyimpan produk, jumlah, harga satuan, dan subtotal pada setiap pesanan. |
| `pengaturan_banner` | Menyimpan konten dan lokasi foto banner homepage; tabel ini dibuat otomatis oleh aplikasi. |

File `database/marketplace.sql` membuat tabel utama dan memasukkan akun admin awal, kategori, penjual, serta produk contoh. Tabel `pengaturan_banner` dibuat otomatis oleh kode aplikasi.

## 11. Cara Instalasi dan Menjalankan Website di XAMPP

### A. Menyiapkan File Proyek

1. Download atau clone repository ini.
2. Ekstrak folder proyek.
3. Letakkan folder `marketplace` di dalam direktori `C:\xampp\htdocs\`.

### B. Menjalankan Server Lokal

1. Buka XAMPP Control Panel.
2. Klik Start pada Apache.
3. Klik Start pada MySQL.
4. Pastikan keduanya berjalan.

### C. Membuat dan Mengimpor Database

1. Buka `http://localhost/phpmyadmin`.
2. Buat database dengan nama `db_marketplace`.
3. Pilih database tersebut.
4. Buka menu Import.
5. Pilih file `database/marketplace.sql`.
6. Jalankan proses import sampai selesai.

### D. Mengatur Koneksi Database

1. Buka folder `config`.
2. Salin `koneksi.example.php`.
3. Ubah nama salinannya menjadi `koneksi.php`.
4. Untuk konfigurasi standar XAMPP, gunakan:
   - Host: `localhost`
   - Username: `root`
   - Password: kosong
   - Nama database: `db_marketplace`

File `config/koneksi.php` tidak disertakan di repository karena dikecualikan oleh `.gitignore`. Setiap pengguna perlu membuat file tersebut berdasarkan konfigurasi lingkungannya sendiri.

### E. Membuka Website

Buka alamat berikut pada browser:

`http://localhost/marketplace/`

Untuk masuk sebagai admin, buka:

`http://localhost/marketplace/admin/login.php`

Gunakan akun administrator awal yang tercantum pada bagian **Akun Administrator Awal**.

## 12. Pengujian Fitur

Pengujian dapat dilakukan dengan memeriksa fungsi-fungsi berikut:

- Halaman utama dan tampilan katalog produk.
- Pencarian dan filter kategori.
- Halaman detail produk.
- Login dan logout admin.
- Penambahan, pengubahan, dan penghapusan produk.
- Upload foto produk.
- Pengubahan banner homepage.
- Penambahan, pengubahan jumlah, dan penghapusan produk dari keranjang.
- Validasi data checkout.
- Penyimpanan pesanan dan detail pesanan.
- Pengurangan stok setelah checkout berhasil.
- Pembaruan status pesanan oleh admin.
- Pembatasan akses keranjang dan checkout ketika admin sedang login.

## 13. Keamanan dan Catatan Repository

- File `config/koneksi.php` berisi pengaturan koneksi pribadi dan tidak boleh diunggah ke repository publik.
- File `.gitignore` telah mengecualikan `config/koneksi.php`.
- Password administrator disimpan dalam bentuk hash pada database, bukan sebagai teks biasa.
- Form yang mengubah data menggunakan token CSRF.
- Query yang menerima input pengguna menggunakan prepared statement pada bagian-bagian yang memerlukannya.
- Ganti password admin awal sebelum website digunakan secara publik.
- Pastikan folder `uploads/produk/` dan `uploads/banner/` dapat ditulisi oleh server agar fitur upload berfungsi.

## 14. Identitas Proyek

| Keterangan | Informasi |
|---|---|
| Nama Aplikasi | Bloomé Flower Shop |
| Jenis Aplikasi | Website Toko Bunga / Marketplace Sederhana |
| Tugas | Tugas 11 – Pengembangan Web |
| Bahasa Pemrograman | PHP Native |
| Database | MySQL |
| Konektor Database | MySQLi |
| Server Lokal | XAMPP |
| Editor | Visual Studio Code |

## 📄 Laporan Tugas 11

Dokumentasi laporan pengembangan website Bloomé Flower Shop dapat diakses melalui tautan Google Docs berikut:

📎 **[Lihat Laporan Tugas 11 – Bloomé Flower Shop](https://docs.google.com/document/d/1GYyjBO2-vPfBnuvBVu72-wepqS-0dJhr/edit?usp=sharing&ouid=117528716079922035380&rtpof=true&sd=true)**

Laporan ini berisi dokumentasi pengerjaan dan penjelasan proyek website marketplace Bloomé Flower Shop.


---

**Bloomé Flower Shop**  
*Flowers made with love, delivered with care.*
