<?php
// backend/tambah_buku.php

// Panggil file koneksi database
require_once 'koneksi.php';

// Set header ke JSON karena frontend (main.js) mengharapkan respons JSON
header('Content-Type: application/json');

// Pastikan metode request adalah POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Metode request tidak diizinkan.']);
    exit();
}

// Validasi dan ambil data
$kode_buku   = isset($_POST['kode_buku']) ? trim($_POST['kode_buku']) : '';
$judul       = isset($_POST['judul']) ? trim($_POST['judul']) : '';
$pengarang   = isset($_POST['pengarang']) ? trim($_POST['pengarang']) : NULL; // Boleh NULL
$penerbit    = isset($_POST['penerbit']) ? trim($_POST['penerbit']) : NULL;   // Boleh NULL
$tahun_terbit= isset($_POST['tahun_terbit']) && $_POST['tahun_terbit'] !== '' ? (int)$_POST['tahun_terbit'] : NULL;
$stok        = isset($_POST['stok']) ? (int)$_POST['stok'] : 0;

// Validasi wajib isi
if (empty($kode_buku) || empty($judul) || $stok < 1) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Kode Buku, Judul, dan Stok wajib diisi dengan benar.']);
    exit();
}

// Persiapan query menggunakan Prepared Statement
$sql = "INSERT INTO buku (kode_buku, judul, pengarang, penerbit, tahun_terbit, stok) 
        VALUES (?, ?, ?, ?, ?, ?)";
        
$stmt = $koneksi->prepare($sql);

if ($stmt === false) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Gagal menyiapkan statement: ' . $koneksi->error]);
    exit();
}

// Bind parameter (s: string, i: integer)
$stmt->bind_param("ssssii", $kode_buku, $judul, $pengarang, $penerbit, $tahun_terbit, $stok);

// Eksekusi query
if ($stmt->execute()) {
    // Sukses
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Buku "' . $judul . '" berhasil ditambahkan ke inventaris!']);
} else {
    // Gagal (misalnya karena Kode Buku sudah ada/UNIQUE)
    $error_message = $koneksi->errno === 1062 ? 'Kode Buku/ISBN sudah terdaftar.' : 'Gagal menyimpan data buku: ' . $stmt->error;
    http_response_code(409); // Conflict
    echo json_encode(['success' => false, 'message' => $error_message]);
}

$stmt->close();
$koneksi->close();
?>