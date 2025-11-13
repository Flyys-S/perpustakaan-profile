<?php
// Panggil file koneksi database
include 'koneksi.php';

// Cek apakah form disubmit dengan method POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Ambil data dari form
    $nama = mysqli_escape_string($conn, $_POST['nama'] ?? '');
    $kelas = mysqli_escape_string($conn, $_POST['kelas'] ?? '');
    $alasan = mysqli_escape_string($conn, $_POST['alasan'] ?? '');
    $status = 'pending';
    
    // Validasi input wajib
    if (empty($nama) || empty($kelas) || empty($alasan)) {
        echo json_encode(["status" => "error", "message" => "Nama, Kelas, dan Alasan wajib diisi!"]);
        exit;
    }
    
    // Handle upload foto (jika ada)
    $foto = '';
    if (!empty($_FILES['foto']['name'])) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        // Buat nama file unik
        $file_ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto_name = "foto_" . time() . "." . $file_ext;
        $foto = $target_dir . $foto_name;
        
        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $foto)) {
            echo json_encode(["status" => "error", "message" => "Gagal upload foto!"]);
            exit;
        }
    }
    
    // Insert data ke database
    $insert_query = "INSERT INTO pendaftaran (nama, kelas, alasan, foto, status, tanggal_daftar) 
                     VALUES ('$nama', '$kelas', '$alasan', '$foto', '$status', NOW())";
    
    if (mysqli_query($conn, $insert_query)) {
        echo json_encode(["status" => "success", "message" => "Pendaftaran berhasil! Tunggu verifikasi dari petugas."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal menyimpan data: " . mysqli_error($conn)]);
    }
    exit;
}
?>
