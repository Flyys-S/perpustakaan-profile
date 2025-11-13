<?php
// ==== Konfigurasi Header (biar bisa diakses frontend lain) ====
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json");

// ==== Tangani Preflight Request (CORS) ====
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ==== Koneksi Database ====
$host = "localhost";
$user = "root";
$pass = "";
$db   = "perpustakaan_db"; // ubah sesuai nama database kamu

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(["error" => "Gagal koneksi ke database: " . $conn->connect_error]));
}

// ==== Jika GET → tampilkan daftar buku ====
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $conn->query("SELECT * FROM buku ORDER BY id DESC");
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);
    exit;
}

// ==== Jika POST → simpan data buku baru ====
$input = json_decode(file_get_contents("php://input"), true);

if (empty($input['judul'])) {
    echo json_encode(["error" => "Judul buku wajib diisi!"]);
    exit;
}

$judul        = $conn->real_escape_string($input['judul']);
$anak_judul   = $conn->real_escape_string($input['anak_judul'] ?? '');
$pengarang    = $conn->real_escape_string($input['pengarang'] ?? '');
$penerbit     = $conn->real_escape_string($input['penerbit'] ?? '');
$tahun_terbit = $conn->real_escape_string($input['tahun_terbit'] ?? '');
$sumber_buku  = $conn->real_escape_string($input['sumber_buku'] ?? '');
$isbn         = $conn->real_escape_string($input['isbn'] ?? '');
$kategori     = $conn->real_escape_string($input['kategori'] ?? '');
$bahasa       = $conn->real_escape_string($input['bahasa'] ?? '');

// ==== Simpan ke tabel ====
$sql = "INSERT INTO buku 
        (judul, anak_judul, pengarang, penerbit, tahun_terbit, sumber_buku, isbn, kategori, bahasa)
        VALUES 
        ('$judul', '$anak_judul', '$pengarang', '$penerbit', '$tahun_terbit', '$sumber_buku', '$isbn', '$kategori', '$bahasa')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["message" => "✅ Buku berhasil ditambahkan!"]);
} else {
    echo json_encode(["error" => "Gagal menyimpan buku: " . $conn->error]);
}

$conn->close();
?>
