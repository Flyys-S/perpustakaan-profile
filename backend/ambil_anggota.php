<?php
// backend/ambil_anggota.php

require_once 'koneksi.php';

// Set header ke JSON
header('Content-Type: application/json');

// Query untuk mengambil semua data anggota
$sql = "SELECT nim_nisn, nama_lengkap, kelas, email, telepon, tgl_daftar FROM anggota ORDER BY tgl_daftar DESC";
$result = $koneksi->query($sql);

if ($result) {
    $data_anggota = [];
    while ($row = $result->fetch_assoc()) {
        $data_anggota[] = $row;
    }
    
    // Sukses, kirim data anggota dalam array JSON
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'total' => count($data_anggota),
        'data' => $data_anggota
    ]);

    $result->free();
} else {
    // Gagal mengambil data
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Gagal mengambil data anggota: ' . $koneksi->error
    ]);
}

$koneksi->close();
?>