<?php
// tambah_buku.php
header('Content-Type: application/json');

// 1. Sertakan file koneksi database
require 'koneksi.php';

// 2. Ambil data dari POST request
$judul = $_POST['judul'] ?? '';
$penulis = $_POST['penulis'] ?? '';
$penerbit = $_POST['penerbit'] ?? null;
$tahun = $_POST['tahun'] ?? '';
$isbn = $_POST['isbn'] ?? null;
$jumlah = (int)($_POST['jumlah'] ?? 0);

// 3. Validasi sederhana
if (empty($judul) || empty($penulis) || empty($tahun) || $jumlah < 1) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Judul, Penulis, Tahun, dan Jumlah wajib diisi.']);
    exit;
}

// 4. Proses penyimpanan ke database
try {
    // Query SQL
    $sql = "INSERT INTO buku (judul, penulis, penerbit, tahun_terbit, isbn, stok) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    
    // Bind dan eksekusi statement
    $stmt->execute([
        $judul, 
        $penulis, 
        $penerbit, 
        $tahun, 
        $isbn, 
        $jumlah
    ]);
    
    // 5. Beri respons sukses
    echo json_encode(['status' => 'success', 'message' => 'Data buku berhasil disimpan!']);

} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    
    // Cek jika error disebabkan oleh duplikasi ISBN
     if ($e->getCode() == '23000' && $isbn) {
        $msg = 'ISBN sudah terdaftar dalam koleksi.';
        http_response_code(409); // Conflict
    } else {
        $msg = 'Gagal menyimpan data ke database. Silakan coba lagi.';
    }

    error_log("Error menyimpan buku: " . $e->getMessage()); 
    echo json_encode(['status' => 'error', 'message' => $msg]);
}
?>