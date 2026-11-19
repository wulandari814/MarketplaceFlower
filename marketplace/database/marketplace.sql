-- Database Bloomé Flower Shop
-- Buat/pilih database terlebih dahulu di phpMyAdmin, lalu import file ini.
-- File ini sengaja tidak memakai CREATE DATABASE / USE agar kompatibel dengan InfinityFree.

DROP TABLE IF EXISTS detail_pesanan;
DROP TABLE IF EXISTS pesanan;
DROP TABLE IF EXISTS produk;
DROP TABLE IF EXISTS pembeli;
DROP TABLE IF EXISTS penjual;
DROP TABLE IF EXISTS kategori;
DROP TABLE IF EXISTS admin;

CREATE TABLE admin (
  id_admin INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE kategori (
  id_kategori INT AUTO_INCREMENT PRIMARY KEY,
  nama_kategori VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE penjual (
  id_penjual INT AUTO_INCREMENT PRIMARY KEY,
  nama_toko VARCHAR(100) NOT NULL,
  nama_pemilik VARCHAR(100) NOT NULL,
  telepon VARCHAR(20),
  alamat TEXT
) ENGINE=InnoDB;

CREATE TABLE produk (
  id_produk INT AUTO_INCREMENT PRIMARY KEY,
  id_kategori INT NOT NULL,
  id_penjual INT NOT NULL,
  nama_produk VARCHAR(150) NOT NULL,
  deskripsi TEXT,
  harga DECIMAL(12,2) NOT NULL,
  stok INT NOT NULL DEFAULT 0,
  gambar VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_produk_kategori FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_produk_penjual FOREIGN KEY (id_penjual) REFERENCES penjual(id_penjual) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE pembeli (
  id_pembeli INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  telepon VARCHAR(20),
  alamat TEXT
) ENGINE=InnoDB;

CREATE TABLE pesanan (
  id_pesanan INT AUTO_INCREMENT PRIMARY KEY,
  kode_pesanan VARCHAR(30) NOT NULL UNIQUE,
  id_pembeli INT NOT NULL,
  tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  total DECIMAL(14,2) NOT NULL,
  status ENUM('baru','diproses','dikirim','selesai','batal') NOT NULL DEFAULT 'baru',
  catatan VARCHAR(255) DEFAULT NULL,
  CONSTRAINT fk_pesanan_pembeli FOREIGN KEY (id_pembeli) REFERENCES pembeli(id_pembeli) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE detail_pesanan (
  id_detail INT AUTO_INCREMENT PRIMARY KEY,
  id_pesanan INT NOT NULL,
  id_produk INT NOT NULL,
  jumlah INT NOT NULL,
  harga_satuan DECIMAL(12,2) NOT NULL,
  subtotal DECIMAL(14,2) NOT NULL,
  CONSTRAINT fk_detail_pesanan FOREIGN KEY (id_pesanan) REFERENCES pesanan(id_pesanan) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_detail_produk FOREIGN KEY (id_produk) REFERENCES produk(id_produk) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ===== Data awal =====
-- Admin: username "admin", password "admin123" (WAJIB diganti setelah deploy)
INSERT INTO admin (username, password_hash) VALUES
('admin', '$2b$12$u6q4tXAdrBrAj5iP8F.VBO2M.2n4RDQV360aLb58E/Exu2RlqCikO');

INSERT INTO kategori (nama_kategori) VALUES
('Bouquet Roses'), ('Tulip & Peony'), ('Lily & Daisy'), ('Flower Box'), ('Fresh Flower');

INSERT INTO penjual (nama_toko, nama_pemilik, telepon, alamat) VALUES
('Bloomé Studio', 'Bloomé Team', '081200000001', 'Bengkulu'),
('Petal House', 'Petal House Team', '081200000002', 'Bengkulu'),
('Rosé Garden', 'Rosé Garden Team', '081200000003', 'Bengkulu');

INSERT INTO produk (id_kategori, id_penjual, nama_produk, deskripsi, harga, stok, gambar) VALUES
(1, 1, 'Pink Sugar Bouquet', 'Mawar pink pastel dengan wrapping pita satin dan sentuhan baby breath. Manis untuk birthday, anniversary, atau hadiah kecil penuh cinta.', 185000, 20, NULL),
(1, 1, 'Strawberry Kiss', 'Buket mawar merah-pink dengan gaya romantis dan playful, cocok untuk kejutan spesial.', 225000, 15, NULL),
(2, 2, 'Cotton Candy Tulips', 'Tulip pastel bernuansa pink, peach, dan cream dengan wrapping soft dan pita mungil.', 275000, 12, NULL),
(2, 2, 'Peony Cloud', 'Peony fluffy bernuansa blush yang terlihat dreamy, lembut, dan super feminin.', 320000, 8, NULL),
(3, 2, 'Milky Lily', 'Lily putih-cream dengan tampilan clean dan soft untuk hadiah yang elegan.', 195000, 10, NULL),
(3, 2, 'Daisy Picnic', 'Daisy mungil bernuansa putih-kuning seperti piknik musim semi yang ceria.', 145000, 25, NULL),
(4, 3, 'Blush Teddy Flower Box', 'Flower box blush pink dengan susunan bunga mungil yang cute untuk hadiah istimewa.', 350000, 7, NULL),
(4, 3, 'Rosie Gift Box', 'Kotak bunga pastel dengan aksen pita manis, cocok untuk surprise.', 285000, 9, NULL),
(5, 1, 'Baby Breath Angel', 'Baby breath putih seperti awan kecil, minimalis dan dreamy.', 125000, 30, NULL);
