<?php
// ==== Koneksi Database (gunakan koneksi.php yang sudah ada) ====
include 'koneksi.php';

// ==== Tangani Preflight Request (CORS) ====
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ==== Jika GET → tampilkan daftar buku ====
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = mysqli_query($conn, "SELECT * FROM buku ORDER BY id DESC");
    $data = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    header("Content-Type: application/json");
    echo json_encode($data);
    exit;
}

// ==== Jika POST → simpan data buku baru ====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header("Content-Type: application/json");
    
    // Ambil data dari form
    $judul        = mysqli_escape_string($conn, $_POST['judul'] ?? '');
    $anak_judul   = mysqli_escape_string($conn, $_POST['anak_judul'] ?? '');
    $nomor_buku   = mysqli_escape_string($conn, $_POST['nomor_buku'] ?? '');
    $pengarang    = mysqli_escape_string($conn, $_POST['pengarang'] ?? '');
    $penerbit     = mysqli_escape_string($conn, $_POST['penerbit'] ?? '');
    $tahun_terbit = mysqli_escape_string($conn, $_POST['tahun_terbit'] ?? '');
    $sumber_buku  = mysqli_escape_string($conn, $_POST['sumber_buku'] ?? '');
    $isbn         = mysqli_escape_string($conn, $_POST['isbn'] ?? '');
    $kategori     = mysqli_escape_string($conn, $_POST['kategori'] ?? '');
    $bahasa       = mysqli_escape_string($conn, $_POST['bahasa'] ?? '');

    // Validasi input wajib
    if (empty($judul)) {
        echo json_encode(["status" => "error", "message" => "Judul buku wajib diisi!"]);
        exit;
    }

    // ==== Simpan ke tabel ====
    $sql = "INSERT INTO buku 
            (judul, anak_judul, nomor_buku, pengarang, penerbit, tahun_terbit, sumber_buku, isbn, kategori, bahasa)
            VALUES 
            ('$judul', '$anak_judul', '$nomor_buku', '$pengarang', '$penerbit', '$tahun_terbit', '$sumber_buku', '$isbn', '$kategori', '$bahasa')";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["status" => "success", "message" => "✅ Buku berhasil ditambahkan!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal menyimpan buku: " . mysqli_error($conn)]);
    }
    exit;
}
?>

