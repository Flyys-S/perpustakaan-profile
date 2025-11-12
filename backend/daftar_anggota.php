<?php
// backend/daftar_anggota.php

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

// =========================================================
// ASUMSI: Form Anggota memiliki field (sesuaikan dengan form_anggota.html Anda)
// - nama_lengkap
// - nim_nisn (Nomor Induk)
// - kelas
// - email
// - telepon
// =========================================================

// Validasi dan ambil data
$nama_lengkap = isset($_POST['nama_lengkap']) ? trim($_POST['nama_lengkap']) : '';
$nim_nisn     = isset($_POST['nim_nisn']) ? trim($_POST['nim_nisn']) : '';
$kelas        = isset($_POST['kelas']) ? trim($_POST['kelas']) : '';
$email        = isset($_POST['email']) ? trim($_POST['email']) : '';
$telepon      = isset($_POST['telepon']) ? trim($_POST['telepon']) : '';

// Validasi sederhana
if (empty($nama_lengkap) || empty($nim_nisn) || empty($kelas)) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Nama, NIM/NISN, dan Kelas wajib diisi.']);
    exit();
}

// Persiapan query menggunakan Prepared Statement untuk mencegah SQL Injection
$sql = "INSERT INTO anggota (nim_nisn, nama_lengkap, kelas, email, telepon, tgl_daftar) 
        VALUES (?, ?, ?, ?, ?, NOW())";
        
$stmt = $koneksi->prepare($sql);

if ($stmt === false) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Gagal menyiapkan statement: ' . $koneksi->error]);
    exit();
}

// Bind parameter (s: string, i: integer, d: double, b: blob)
$stmt->bind_param("sssss", $nim_nisn, $nama_lengkap, $kelas, $email, $telepon);

// Eksekusi query
if ($stmt->execute()) {
    // Sukses
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Pendaftaran anggota berhasil! Selamat datang di Perpustakaan.']);
} else {
    // Gagal (misalnya karena NIM/NISN sudah ada jika kolom itu UNIQUE)
    $error_message = $koneksi->errno === 1062 ? 'NIM/NISN sudah terdaftar.' : 'Gagal menyimpan data: ' . $stmt->error;
    http_response_code(409); // Conflict
    echo json_encode(['success' => false, 'message' => $error_message]);
}

$stmt->close();
$koneksi->close();
?>