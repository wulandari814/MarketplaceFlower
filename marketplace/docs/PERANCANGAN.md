# Dokumen Perancangan — Web Marketplace Tradisional (Soal 11)

## a. Entitas
| No | Entitas | Keterangan |
|----|---------|------------|
| 1 | **kategori** | Pengelompokan produk (sayur & buah, bumbu, kerajinan, dst.) |
| 2 | **penjual** | Toko/penjual yang menawarkan produk |
| 3 | **produk** | Barang yang dijual |
| 4 | **pembeli** | Orang yang melakukan pemesanan |
| 5 | **pesanan** | Header transaksi (kode, tanggal, total, status) |
| 6 | **detail_pesanan** | Rincian produk pada tiap pesanan |
| 7 | **admin** | Pengelola yang login untuk CRUD produk & mengelola pesanan |

## b. Rancangan Database Relasional
**kategori** (PK `id_kategori`) — nama_kategori

**penjual** (PK `id_penjual`) — nama_toko, nama_pemilik, telepon, alamat

**produk** (PK `id_produk`; FK `id_kategori` → kategori, FK `id_penjual` → penjual) — nama_produk, deskripsi, harga, stok, gambar, created_at

**pembeli** (PK `id_pembeli`; `email` UNIQUE) — nama, email, telepon, alamat

**pesanan** (PK `id_pesanan`; `kode_pesanan` UNIQUE; FK `id_pembeli` → pembeli) — tanggal, total, status (baru/diproses/dikirim/selesai/batal), catatan

**detail_pesanan** (PK `id_detail`; FK `id_pesanan` → pesanan, FK `id_produk` → produk) — jumlah, harga_satuan, subtotal

**admin** (PK `id_admin`; `username` UNIQUE) — password_hash

Hubungan antar-tabel:
- kategori **1 : N** produk
- penjual **1 : N** produk
- pembeli **1 : N** pesanan
- pesanan **1 : N** detail_pesanan
- produk **1 : N** detail_pesanan (relasi **M : N** pesanan–produk dipecah lewat `detail_pesanan`)

`harga_satuan` disimpan di detail agar riwayat pesanan tidak berubah saat harga produk diubah.

## c. ERD
```mermaid
erDiagram
    KATEGORI ||--o{ PRODUK : "memiliki"
    PENJUAL  ||--o{ PRODUK : "menjual"
    PEMBELI  ||--o{ PESANAN : "membuat"
    PESANAN  ||--|{ DETAIL_PESANAN : "berisi"
    PRODUK   ||--o{ DETAIL_PESANAN : "dipesan_di"

    KATEGORI { int id_kategori PK
               varchar nama_kategori }
    PENJUAL { int id_penjual PK
              varchar nama_toko
              varchar nama_pemilik
              varchar telepon
              text alamat }
    PRODUK { int id_produk PK
             int id_kategori FK
             int id_penjual FK
             varchar nama_produk
             text deskripsi
             decimal harga
             int stok
             varchar gambar }
    PEMBELI { int id_pembeli PK
              varchar nama
              varchar email UK
              varchar telepon
              text alamat }
    PESANAN { int id_pesanan PK
              varchar kode_pesanan UK
              int id_pembeli FK
              timestamp tanggal
              decimal total
              enum status }
    DETAIL_PESANAN { int id_detail PK
                     int id_pesanan FK
                     int id_produk FK
                     int jumlah
                     decimal harga_satuan
                     decimal subtotal }
```
> Cara membuat gambar: tempel blok di atas ke https://mermaid.live lalu ekspor PNG untuk laporan.
> (Tabel `admin` berdiri sendiri, tanpa relasi, jadi tidak digambar.)

## d. Alur Proses Transaksi
```mermaid
flowchart TD
    A([Mulai]) --> B[Pembeli membuka daftar produk]
    B --> C[Pilih produk / lihat detail]
    C --> D{Stok tersedia?}
    D -- Tidak --> C
    D -- Ya --> E[Tambah ke keranjang]
    E --> F{Belanja lagi?}
    F -- Ya --> B
    F -- Tidak --> G[Buka keranjang, ubah/hapus item]
    G --> H[Checkout: isi nama, email, alamat]
    H --> I{Data valid?}
    I -- Tidak --> H
    I -- Ya --> J[Mulai transaksi DB]
    J --> K{Stok semua item cukup?}
    K -- Tidak --> L[Rollback, tampilkan pesan error] --> G
    K -- Ya --> M[Simpan pembeli, pesanan, detail_pesanan]
    M --> N[Kurangi stok produk]
    N --> O[Commit, kosongkan keranjang]
    O --> P[Tampilkan kode pesanan] --> Q([Selesai])
```

## e. Mockup UI (wireframe)

**Daftar produk**
```
+--------------------------------------------------------------+
| Pasar Online Bengkulu              [Keranjang (2)] [Login]   |
+--------------------------------------------------------------+
| [ Cari produk...            ] [ Semua kategori v ] [ Cari ]  |
|                                                              |
| +-------------+  +-------------+  +-------------+            |
| |   gambar    |  |   gambar    |  |   gambar    |            |
| | [Kategori]  |  | [Kategori]  |  | [Kategori]  |            |
| | Nama produk |  | Nama produk |  | Nama produk |            |
| | Nama toko   |  | Nama toko   |  | Nama toko   |            |
| | Rp 25.000   |  | Rp 30.000   |  | Rp 18.000   |            |
| |[Detail][+K] |  |[Detail][+K] |  |[Detail][+K] |            |
| +-------------+  +-------------+  +-------------+            |
+--------------------------------------------------------------+
```
**Detail produk**
```
+--------------------------------------------------------------+
| < Kembali ke daftar                                          |
| +------------------+   [Kategori]                            |
| |                  |   Nama Produk                           |
| |     gambar       |   Rp 25.000                             |
| |                  |   Deskripsi produk ...                  |
| +------------------+   Penjual: Toko Tani Makmur             |
|                        Stok: 40                              |
|                        [ 1 ] [Tambah ke Keranjang]          |
+--------------------------------------------------------------+
```
**Keranjang belanja**
```
+--------------------------------------------------------------+
| Keranjang Belanja                                            |
| Produk          Harga      Jumlah      Subtotal              |
| Pisang Kepok    Rp 25.000  [2][OK]     Rp 50.000   [Hapus]   |
| Lada Hitam      Rp 22.000  [1][OK]     Rp 22.000   [Hapus]   |
|                              Total     Rp 72.000             |
| [Lanjut Belanja]                         [Checkout]          |
+--------------------------------------------------------------+
```
