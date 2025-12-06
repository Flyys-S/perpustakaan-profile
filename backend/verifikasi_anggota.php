<?php
session_start();
include 'koneksi.php';

// PROTEKSI: Hanya admin yang boleh akses
if (!isset($_SESSION['login'])) {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Akses Ditolak!"]);
    exit;
}

// === POST: Update Status (Terima/Tolak) ===
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? '';
    $aksi = $_POST['aksi'] ?? ''; 

    if (empty($id) || !in_array($aksi, ['terima', 'tolak'])) {
        echo json_encode(["status" => "error", "message" => "Input tidak valid"]);
        exit;
    }

    $status_baru = ($aksi == 'terima') ? 'active' : 'rejected';
    
    // Cukup Update Status! Tidak perlu pindah tabel.
    $stmt = $conn->prepare("UPDATE anggota SET status = ?, tanggal_verifikasi = NOW() WHERE id = ?");
    $stmt->bind_param("si", $status_baru, $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Status anggota berhasil diubah menjadi: " . $status_baru]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal update: " . $stmt->error]);
    }
    $stmt->close();
    exit;
}

// === GET: Ambil Data Pending ===
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Ambil hanya yang statusnya 'pending'
    $result = mysqli_query($conn, "SELECT * FROM anggota WHERE status = 'pending' ORDER BY tanggal_daftar DESC");
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode(["status" => "success", "data" => $data]);
    exit;
}
?>