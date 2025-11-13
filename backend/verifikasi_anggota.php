<?php
// Panggil file koneksi database
include 'koneksi.php';

// Cek apakah request adalah POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Ambil data dari request
    $id = mysqli_escape_string($conn, $_POST['id'] ?? '');
    $aksi = mysqli_escape_string($conn, $_POST['aksi'] ?? ''); // 'terima' atau 'tolak'
    
    // Validasi input
    if (empty($id) || empty($aksi)) {
        echo json_encode(["status" => "error", "message" => "Data tidak lengkap!"]);
        exit;
    }
    
    if ($aksi == 'terima') {
        // Ambil data pendaftar dari tabel daftar
        $query = "SELECT * FROM daftar WHERE id = '$id'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            
            // Update status menjadi 'diterima'
            $update_query = "UPDATE daftar SET status = 'diterima' WHERE id = '$id'";
            
            if (mysqli_query($conn, $update_query)) {
                echo json_encode(["status" => "success", "message" => "Pendaftaran diterima! Data anggota sudah aktif."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Gagal memperbarui status: " . mysqli_error($conn)]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Data pendaftar tidak ditemukan!"]);
        }
        
    } elseif ($aksi == 'tolak') {
        // Update status menjadi 'ditolak'
        $update_query = "UPDATE daftar SET status = 'ditolak' WHERE id = '$id'";
        
        if (mysqli_query($conn, $update_query)) {
            echo json_encode(["status" => "success", "message" => "Pendaftaran ditolak!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal memperbarui status: " . mysqli_error($conn)]);
        }
        
    } else {
        echo json_encode(["status" => "error", "message" => "Aksi tidak valid!"]);
    }
    
    exit;
}

// GET request - untuk menampilkan daftar pendaftar yang menunggu verifikasi
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $query = "SELECT * FROM daftar WHERE status = 'pending' ORDER BY tanggal_daftar DESC";
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        echo json_encode(["status" => "success", "data" => $data]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal mengambil data: " . mysqli_error($conn)]);
    }
    
    exit;
}
?>
