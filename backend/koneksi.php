<?php
// backend/koneksi.php

$host = "localhost"; // Biasanya localhost
$user = "root";      // Ganti dengan username database Anda
$pass = "";          // Ganti dengan password database Anda
$db   = "perpustakaan_db"; // Ganti dengan nama database Anda

// Buat koneksi
$koneksi = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($koneksi->connect_error) {
    // Jika gagal, tampilkan pesan error dalam format JSON
    header('Content-Type: application/json');
    http_response_code(500); // Internal Server Error
    echo json_encode([
        'success' => false,
        'message' => 'Koneksi database gagal: ' . $koneksi->connect_error
    ]);
    exit(); // Hentikan eksekusi script
}
?>