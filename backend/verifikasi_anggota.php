<?php
// Panggil file koneksi database
include 'koneksi.php';

// Cek apakah request adalah POST (untuk aksi terima/tolak)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Ambil data dari request
    $id = $_POST['id'] ?? '';
    $aksi = $_POST['aksi'] ?? ''; // 'terima' atau 'tolak'
    
    // Validasi input
    if (empty($id) || empty($aksi)) {
        echo json_encode(["status" => "error", "message" => "Data ID atau Aksi tidak lengkap!"]);
        exit;
    }
    
    // ===================================================
    // === LOGIKA "TERIMA" DENGAN TRANSAKSI & INSERT ===
    // ===================================================
    if ($aksi == 'terima') {
        
        // Mulai transaksi untuk memastikan integritas data
        mysqli_begin_transaction($conn);

        try {
            // 1. Ambil data pendaftar dari tabel 'daftar'
            $stmt_select = $conn->prepare("SELECT nama, kelas FROM daftar WHERE id = ? AND status = 'pending'");
            $stmt_select->bind_param("i", $id);
            $stmt_select->execute();
            $result = $stmt_select->get_result();
            
            if ($result->num_rows == 0) {
                // Jika data tidak ditemukan (mungkin sudah diproses)
                mysqli_rollback($conn);
                echo json_encode(["status" => "error", "message" => "Data pendaftar tidak ditemukan atau sudah diproses."]);
                exit;
            }
            
            $row = $result->fetch_assoc();
            $nama = $row['nama'];
            $kelas = $row['kelas'];
            
            // 2. Masukkan data ke tabel 'anggota'
            // (Asumsi tabel 'anggota' memiliki kolom nama, kelas, dan tanggal_bergabung)
            $stmt_insert = $conn->prepare("INSERT INTO anggota (nama, kelas, tanggal_bergabung) VALUES (?, ?, NOW())");
            $stmt_insert->bind_param("ss", $nama, $kelas);
            $exec_insert = $stmt_insert->execute();
            
            // 3. Update status di tabel 'daftar' menjadi 'diterima'
            $stmt_update = $conn->prepare("UPDATE daftar SET status = 'diterima' WHERE id = ?");
            $stmt_update->bind_param("i", $id);
            $exec_update = $stmt_update->execute();
            
            // 4. Commit transaksi jika kedua operasi berhasil
            if ($exec_insert && $exec_update) {
                mysqli_commit($conn);
                echo json_encode(["status" => "success", "message" => "Pendaftaran diterima! Anggota baru berhasil ditambahkan."]);
            } else {
                // Jika salah satu gagal, batalkan semua
                mysqli_rollback($conn);
                echo json_encode(["status" => "error", "message" => "Gagal memproses data: " . $conn->error]);
            }

            // Tutup statements
            $stmt_select->close();
            $stmt_insert->close();
            $stmt_update->close();

        } catch (Exception $e) {
            // Tangani jika ada error tak terduga
            mysqli_rollback($conn);
            echo json_encode(["status" => "error", "message" => "Terjadi kesalahan: " . $e->getMessage()]);
        }
        
    // ===================================================
    // === LOGIKA "TOLAK" (DIAMANKAN) ===
    // ===================================================
    } elseif ($aksi == 'tolak') {
        
        // Update status menjadi 'ditolak' menggunakan prepared statement
        $stmt_tolak = $conn->prepare("UPDATE daftar SET status = 'ditolak' WHERE id = ?");
        $stmt_tolak->bind_param("i", $id);
        
        if ($stmt_tolak->execute()) {
            echo json_encode(["status" => "success", "message" => "Pendaftaran ditolak."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal memperbarui status: " . $conn->error]);
        }
        $stmt_tolak->close();
        
    } else {
        echo json_encode(["status" => "error", "message" => "Aksi tidak valid!"]);
    }
    
    exit;
}

// ===================================================
// === LOGIKA "GET" (TIDAK BERUBAH, HANYA MEMUAT DAFTAR) ===
// ===================================================
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