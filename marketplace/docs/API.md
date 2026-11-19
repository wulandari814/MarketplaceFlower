# Dokumen Rancangan REST API — Marketplace (Soal 12)

## a. Fungsi Web Service/API dan alasan diperlukan
Web Service/API adalah antarmuka yang membuat data dan fungsi aplikasi dapat diakses aplikasi lain lewat HTTP dengan format standar (JSON), tanpa perlu tahu struktur database atau kode internalnya. Pada marketplace, API dibutuhkan agar:
1. **Aplikasi mobile** (Android/iOS) dapat menampilkan produk dan membuat pesanan dari data yang sama dengan web.
2. **Pihak ketiga** (mis. agregator harga, aplikasi kurir, dashboard toko) dapat mengintegrasikan data tanpa akses langsung ke database.
3. **Keamanan terjaga**: aplikasi luar tidak memegang kredensial database; semua akses lewat validasi di API.
4. **Satu sumber data** dipakai banyak klien sehingga data konsisten, dan format JSON tidak bergantung bahasa pemrograman klien.

## b. Daftar Endpoint
| No | Endpoint | Fungsi |
|----|----------|--------|
| 1 | `GET /api/produk.php` | Menampilkan seluruh produk (opsional `?kategori=ID`, `?q=kata`) |
| 2 | `GET /api/produk.php?id=1` | Menampilkan detail produk |
| 3 | `GET /api/kategori.php` | Menampilkan daftar kategori + jumlah produk |
| 4 | `POST /api/pesanan.php` | Membuat pesanan baru |
| 5 | `GET /api/pesanan.php?kode=ORD-...` | Menampilkan detail pesanan |

Kode status: `200` OK, `201` dibuat, `400` parameter salah, `404` tidak ditemukan, `405` metode tidak diizinkan, `409` stok tidak cukup, `422` data tidak valid, `500` kesalahan server.

## c. Struktur JSON
**Daftar produk** — `GET /api/produk.php`
```json
{
  "status": "success",
  "jumlah": 2,
  "data": [
    { "id_produk": 1, "id_kategori": 1, "nama_kategori": "Sayur & Buah",
      "nama_produk": "Pisang Kepok 1 Sisir", "deskripsi": "Pisang kepok segar langsung dari kebun.",
      "harga": 25000, "stok": 40, "gambar": null, "penjual": "Toko Tani Makmur" },
    { "id_produk": 2, "...": "..." }
  ]
}
```
**Detail produk** — `GET /api/produk.php?id=1`
```json
{
  "status": "success",
  "data": { "id_produk": 1, "id_kategori": 1, "nama_kategori": "Sayur & Buah",
            "nama_produk": "Pisang Kepok 1 Sisir", "deskripsi": "Pisang kepok segar langsung dari kebun.",
            "harga": 25000, "stok": 40, "gambar": null, "penjual": "Toko Tani Makmur" }
}
```
**Buat pesanan** — `POST /api/pesanan.php` (Header `Content-Type: application/json`)
```json
{
  "nama": "Ardeya Wulandari", "email": "ardeya@example.com",
  "telepon": "081234567890", "alamat": "Jl. Contoh No. 1, Bengkulu",
  "catatan": "Tolong dikemas rapi",
  "items": [ { "id_produk": 1, "jumlah": 2 }, { "id_produk": 4, "jumlah": 1 } ]
}
```
Respons `201`:
```json
{ "status": "success", "message": "Pesanan berhasil dibuat.",
  "data": { "kode_pesanan": "ORD-20261118-A1B2C", "total": 72000, "status": "baru" } }
```
Respons error (contoh `404`):
```json
{ "status": "error", "message": "Produk dengan id 999 tidak ditemukan." }
```

## d–g. Menjalankan & menguji di XAMPP
1. Salin folder proyek ke `C:\xampp\htdocs\marketplace` lalu salin `config/koneksi.example.php` → `config/koneksi.php`.
2. Start **Apache** dan **MySQL** di XAMPP Control Panel.
3. Buka `http://localhost/phpmyadmin` → Import `database/marketplace.sql`.
4. Uji di browser: `http://localhost/marketplace/api/produk.php`

**Skenario uji (Postman)** — kerjakan dan screenshot sendiri:
| # | Request | Hasil yang diharapkan |
|---|---------|-----------------------|
| 1 | GET `http://localhost/marketplace/api/produk.php` | 200, daftar 9 produk |
| 2 | GET `.../api/produk.php?id=1` | 200, detail produk 1 |
| 3 | GET `.../api/produk.php?id=999` | 404, pesan tidak ditemukan |
| 4 | GET `.../api/produk.php?id=abc` | 400, parameter salah |
| 5 | GET `.../api/kategori.php` | 200, 5 kategori |
| 6 | POST `.../api/pesanan.php` body JSON contoh di atas (Body → raw → JSON) | 201 + kode pesanan |
| 7 | POST dengan `jumlah` melebihi stok | 409 stok tidak cukup |
| 8 | POST dengan body kosong/email salah | 422 / 400 |
| 9 | GET `.../api/pesanan.php?kode=<kode dari no.6>` | 200, rincian pesanan |
| 10 | Cek phpMyAdmin: tabel `pesanan`, `detail_pesanan`, kolom `stok` produk berkurang | Data tersimpan |

## h. Checklist screenshot laporan
- [ ] URL endpoint di address bar browser (localhost)
- [ ] Postman: method, URL, header, body request
- [ ] Postman: response JSON + status code
- [ ] phpMyAdmin: isi tabel `pesanan` & `detail_pesanan` setelah POST
- [ ] XAMPP Control Panel (Apache & MySQL running)
