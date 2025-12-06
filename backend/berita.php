<?php
session_start();
include 'koneksi.php';

// === GET BERITA (Public) ===
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Urutkan dari yang paling baru
    $result = mysqli_query($conn, "SELECT * FROM berita ORDER BY tanggal DESC");
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
    exit;
}

// === POST (TAMBAH & HAPUS - ADMIN ONLY) ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (!isset($_SESSION['login'])) {
        http_response_code(403);
        echo json_encode(["status" => "error", "message" => "Akses ditolak!"]);
        exit;
    }

    $aksi = $_POST['aksi'] ?? 'tambah';

    // --- HAPUS BERITA ---
    if ($aksi == 'hapus') {
        $id = $_POST['id'];
        // Ambil nama gambar dulu untuk dihapus fisiknya (opsional)
        $q = mysqli_query($conn, "SELECT gambar FROM berita WHERE id='$id'");
        $img = mysqli_fetch_assoc($q)['gambar'];
        if ($img && file_exists("uploads/$img")) unlink("uploads/$img");

        $conn->query("DELETE FROM berita WHERE id='$id'");
        echo json_encode(["status" => "success", "message" => "Berita dihapus!"]);
        exit;
    }

    // --- TAMBAH BERITA ---
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $isi = $_POST['isi'];
    $tanggal = $_POST['tanggal'];
    $penulis = $_POST['penulis'];

    // Upload Gambar
    $gambar_db = '';
    if (!empty($_FILES['gambar']['name'])) {
        $target_dir = "uploads/";
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $new_name = "news_" . time() . "." . $ext;
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_dir . $new_name)) {
            $gambar_db = $new_name;
        }
    }

    $stmt = $conn->prepare("INSERT INTO berita (judul, deskripsi, isi_full, tanggal, penulis, gambar) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $judul, $deskripsi, $isi, $tanggal, $penulis, $gambar_db);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Berita berhasil diterbitkan!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal: " . $stmt->error]);
    }
    exit;
}
?>