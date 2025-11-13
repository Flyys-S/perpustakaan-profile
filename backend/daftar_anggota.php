<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    http_response_code(200);
    exit();
}

$nama = $_POST['nama'] ?? '';
$kelas = $_POST['kelas'] ?? '';
$alasan = $_POST['alasan'] ?? '';
$status = 'pending';

// Upload foto (jika ada)
$foto = '';
if (!empty($_FILES['foto']['name'])) {
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $foto = $target_dir . basename($_FILES["foto"]["name"]);
    move_uploaded_file($_FILES["foto"]["tmp_name"], $foto);
}

if ($nama == '' || $kelas == '' || $alasan == '') {
    echo json_encode(["success" => false, "message" => "Semua kolom wajib diisi."]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO pendaftaran (nama, kelas, alasan, foto, status, tanggal_daftar) VALUES (?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("sssss", $nama, $kelas, $alasan, $foto, $status);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Pendaftaran berhasil, menunggu verifikasi."]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal menyimpan data."]);
}
?>
