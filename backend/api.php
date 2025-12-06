<?php
session_start();
include 'koneksi.php';

// === GET DATA BUKU ===
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $stmt = $conn->prepare("SELECT * FROM buku WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        echo json_encode($result->fetch_assoc());
    } else {
        $result = mysqli_query($conn, "SELECT * FROM buku ORDER BY id DESC");
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) { $data[] = $row; }
        echo json_encode($data);
    }
    exit;
}

// === POST DATA (TAMBAH/UPDATE/HAPUS) ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['login'])) {
        http_response_code(403);
        echo json_encode(["status" => "error", "message" => "Akses ditolak!"]);
        exit;
    }

    $aksi = $_POST['aksi'] ?? 'tambah'; 

    // --- HAPUS ---
    if ($aksi == 'hapus') {
        $id = $_POST['id'] ?? '';
        // Opsional: Hapus file fisik gambar jika mau
        $stmt = $conn->prepare("DELETE FROM buku WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) echo json_encode(["status" => "success", "message" => "Terhapus!"]);
        else echo json_encode(["status" => "error", "message" => "Gagal: " . $conn->error]);
        exit;
    }

    // --- AMBIL INPUT FORM ---
    $judul = $_POST['judul'] ?? '';
    $anak_judul = $_POST['anak_judul'] ?? '';
    $nomor_buku = $_POST['nomor_buku'] ?? '';
    $pengarang = $_POST['pengarang'] ?? '';
    $penerbit = $_POST['penerbit'] ?? '';
    $tahun_terbit = $_POST['tahun_terbit'] ?? '';
    $sumber_buku = $_POST['sumber_buku'] ?? '';
    $isbn = $_POST['isbn'] ?? '';
    $kategori = $_POST['kategori'] ?? '';
    $bahasa = $_POST['bahasa'] ?? '';
    $stok = $_POST['stok'] ?? 10;

    // --- LOGIKA UPLOAD GAMBAR ---
    $cover_file = null; 
    if (!empty($_FILES['cover']['name'])) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        
        $ext = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
        $valid_ext = ['jpg', 'jpeg', 'png', 'webp'];
        
        if(in_array($ext, $valid_ext)) {
            // Nama file unik: waktu_unik.jpg
            $new_name = time() . "_" . uniqid() . "." . $ext;
            if(move_uploaded_file($_FILES['cover']['tmp_name'], $target_dir . $new_name)) {
                $cover_file = $new_name;
            }
        }
    }

    // --- UPDATE ---
    if ($aksi == 'update') {
        $id = $_POST['id'] ?? '';
        
        if ($cover_file) {
            // Jika ada upload gambar baru, update kolom cover
            $sql = "UPDATE buku SET judul=?, anak_judul=?, nomor_buku=?, pengarang=?, penerbit=?, tahun_terbit=?, sumber_buku=?, isbn=?, kategori=?, bahasa=?, stok=?, cover=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssssssisi", $judul, $anak_judul, $nomor_buku, $pengarang, $penerbit, $tahun_terbit, $sumber_buku, $isbn, $kategori, $bahasa, $stok, $cover_file, $id);
        } else {
            // Jika tidak ada gambar baru, jangan ubah kolom cover
            $sql = "UPDATE buku SET judul=?, anak_judul=?, nomor_buku=?, pengarang=?, penerbit=?, tahun_terbit=?, sumber_buku=?, isbn=?, kategori=?, bahasa=?, stok=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssssssii", $judul, $anak_judul, $nomor_buku, $pengarang, $penerbit, $tahun_terbit, $sumber_buku, $isbn, $kategori, $bahasa, $stok, $id);
        }

        if ($stmt->execute()) echo json_encode(["status" => "success", "message" => "Buku berhasil diperbarui!"]);
        else echo json_encode(["status" => "error", "message" => "Gagal: " . $stmt->error]);
        exit;
    }

    // --- TAMBAH BARU ---
    $sql = "INSERT INTO buku (judul, anak_judul, nomor_buku, pengarang, penerbit, tahun_terbit, sumber_buku, isbn, kategori, bahasa, stok, cover) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssis", $judul, $anak_judul, $nomor_buku, $pengarang, $penerbit, $tahun_terbit, $sumber_buku, $isbn, $kategori, $bahasa, $stok, $cover_file);

    if ($stmt->execute()) echo json_encode(["status" => "success", "message" => "Buku berhasil ditambahkan!"]);
    else echo json_encode(["status" => "error", "message" => "Gagal: " . $stmt->error]);
    exit;
}
?>