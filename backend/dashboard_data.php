<?php
session_start();
include 'koneksi.php';

// Proteksi
if (!isset($_SESSION['login'])) {
    http_response_code(403);
    exit;
}

// 1. Total Anggota Aktif
$q1 = mysqli_query($conn, "SELECT COUNT(*) as total FROM anggota WHERE status = 'active'");
$d1 = mysqli_fetch_assoc($q1);

// 2. Total Judul Buku
$q2 = mysqli_query($conn, "SELECT COUNT(*) as total FROM buku");
$d2 = mysqli_fetch_assoc($q2);

// 3. STATISTIK TRANSAKSI (3 STATUS)
// Hitung Pending (Permintaan)
$q_pending = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE status = 'pending'");
$d_pending = mysqli_fetch_assoc($q_pending);

// Hitung Disetujui (Siap Diambil)
$q_siap = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE status = 'disetujui'");
$d_siap = mysqli_fetch_assoc($q_siap);

// Hitung Dipinjam (Sedang dibawa)
$q_pinjam = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE status = 'dipinjam'");
$d_pinjam = mysqli_fetch_assoc($q_pinjam);

// Kirim JSON
echo json_encode([
    "status" => "success",
    "total_anggota" => $d1['total'],
    "total_buku" => $d2['total'],
    
    // Data Transaksi Terpisah
    "trx_pending" => $d_pending['total'],
    "trx_siap" => $d_siap['total'],
    "trx_dipinjam" => $d_pinjam['total']
]);
?>