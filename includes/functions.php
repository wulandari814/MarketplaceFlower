<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function rupiah($n) { return 'Rp ' . number_format((float)$n, 0, ',', '.'); }
function redirect($url) { header('Location: ' . $url); exit; }

function csrf_token() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return $_SESSION['csrf'];
}
function csrf_field() { return '<input type="hidden" name="csrf" value="' . csrf_token() . '">'; }
function csrf_check() {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(400);
        exit('Token tidak valid.');
    }
}
function flash($msg = null, $type = 'success') {
    if ($msg !== null) { $_SESSION['flash'] = [$msg, $type]; return; }
    if (!empty($_SESSION['flash'])) { $f = $_SESSION['flash']; unset($_SESSION['flash']); return $f; }
    return null;
}
function gambar_src($g) { return $g ? $g : 'assets/img/flower-placeholder.svg'; }
function cart_count() { return array_sum($_SESSION['cart'] ?? []); }
function is_admin() { return !empty($_SESSION['admin_id']); }
function require_admin() { if (!is_admin()) redirect('login.php'); }

// Halaman belanja hanya boleh digunakan oleh pembeli (pengunjung), bukan admin.
function require_customer() {
    if (is_admin()) {
        flash('Akun admin hanya dapat mengelola toko. Silakan logout untuk berbelanja sebagai pembeli.', 'warning');
        redirect('admin/produk.php');
    }
}
