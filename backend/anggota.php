<?php
session_start();
include 'koneksi.php';

// PROTEKSI
if (!isset($_SESSION['login'])) {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Akses Ditolak!"]);
    exit;
}

// === GET: AMBIL DATA ANGGOTA ===
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    
    // Jika ada ID, ambil 1 data (Untuk Form Edit)
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $stmt = $conn->prepare("SELECT id, nisn, nama, kelas FROM anggota WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        echo json_encode($data);
    } 
    // Jika tidak ada ID, ambil semua data Active (Untuk Tabel Admin)
    else {
        $query = "SELECT * FROM anggota WHERE status = 'active' ORDER BY nama ASC";
        $result = mysqli_query($conn, $query);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        echo json_encode(["status" => "success", "data" => $data]);
    }
    exit;
}

// === POST: HAPUS & UPDATE ===
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    
    // --- LOGIKA HAPUS ---
    if ($aksi == 'hapus') {
        $id = $_POST['id'] ?? '';
        $stmt = $conn->prepare("DELETE FROM anggota WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Data anggota berhasil dihapus!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal menghapus: " . $conn->error]);
        }
        exit;
    }

    // --- LOGIKA UPDATE ---
    if ($aksi == 'update') {
        $id = $_POST['id'] ?? '';
        $nisn = $_POST['nisn'] ?? '';
        $nama = $_POST['nama'] ?? '';
        $kelas = $_POST['kelas'] ?? '';

        // Validasi sederhana
        if (empty($nama) || empty($nisn)) {
            echo json_encode(["status" => "error", "message" => "Nama dan NISN tidak boleh kosong!"]);
            exit;
        }

        $stmt = $conn->prepare("UPDATE anggota SET nisn=?, nama=?, kelas=? WHERE id=?");
        $stmt->bind_param("sssi", $nisn, $nama, $kelas, $id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Data anggota berhasil diperbarui!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal update: " . $stmt->error]);
        }
        exit;
    }
}
?>