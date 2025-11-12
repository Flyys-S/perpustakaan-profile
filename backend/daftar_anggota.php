<?php
// daftar_anggota.php

// === DEBUGGING START ===
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// KOMENTARI header JSON agar error PHP terlihat jelas di browser
// header('Content-Type: application/json');
// === DEBUGGING END ===

// 1. Sertakan file koneksi database
require 'koneksi.php';

// ... (lanjutkan kode, tidak perlu diubah) ...

// 4. Proses penyimpanan ke database
try {
    // ... (kode query INSERT) ...
    
    // Pastikan Anda menghapus semua 'echo' atau 'print_r' debugging sebelumnya
    // Hapus juga baris 'echo json_encode' jika Anda memasukkannya di luar try/catch
    
    // ...
    
    // 5. Beri respons sukses (TIDAK AKAN TERCAPAI jika ada error sebelum baris ini)
    if (!headers_sent()) {
        header('Content-Type: application/json');
    }
    echo json_encode(['status' => 'success', 'message' => 'Data anggota berhasil didaftarkan!']);

} catch (PDOException $e) {
    http_response_code(500);
    // Tampilkan pesan error di browser saat debugging
    echo "FATAL DB ERROR: " . $e->getMessage();
    
    // ... (lanjutkan kode penanganan error) ...
    
    // Hapus baris 'echo json_encode' yang asli di sini agar tidak ada double output
    // Hentikan eksekusi setelah catch
    exit; 
}
?>