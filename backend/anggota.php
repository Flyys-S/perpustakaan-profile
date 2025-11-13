<?php
// Panggil file koneksi database
include 'koneksi.php'; // <-- INI YANG DIPERBAIKI (sebelumnya config.php)

// Cek apakah request adalah GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    
    $query = "SELECT * FROM anggota ORDER BY id DESC";
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        // Kirim data sebagai JSON
        echo json_encode(["status" => "success", "data" => $data]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal mengambil data: " . mysqli_error($conn)]);
    }
    
    exit;
} else {
    // Jika bukan GET, kirim error
    echo json_encode(["status" => "error", "message" => "Metode request harus GET"]);
    exit;
}
?>