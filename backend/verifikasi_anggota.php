<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    http_response_code(200);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? '';
$aksi = $data['aksi'] ?? ''; // terima atau tolak

if ($aksi == 'terima') {
    $q = $conn->prepare("SELECT * FROM pendaftaran WHERE id = ?");
    $q->bind_param("i", $id);
    $q->execute();
    $result = $q->get_result();
    $p = $result->fetch_assoc();

    if ($p) {
        $insert = $conn->prepare("INSERT INTO anggota (nama, kelas, jabatan, email, tanggal_gabung) VALUES (?, ?, 'anggota', '', NOW())");
        $insert->bind_param("ss", $p['nama'], $p['kelas']);
        $insert->execute();

        $update = $conn->prepare("UPDATE pendaftaran SET status = 'diterima' WHERE id = ?");
        $update->bind_param("i", $id);
        $update->execute();

        echo json_encode(["success" => true, "message" => "Anggota diterima dan dipindahkan ke tabel anggota."]);
    } else {
        echo json_encode(["success" => false, "message" => "Data tidak ditemukan."]);
    }
} elseif ($aksi == 'tolak') {
    $update = $conn->prepare("UPDATE pendaftaran SET status = 'ditolak' WHERE id = ?");
    $update->bind_param("i", $id);
    $update->execute();

    echo json_encode(["success" => true, "message" => "Pendaftaran ditolak."]);
} else {
    echo json_encode(["success" => false, "message" => "Aksi tidak valid."]);
}
?>
