<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Username/Password kosong!"]);
        exit;
    }

    // Gunakan Prepared Statement (Anti SQL Injection)
    $stmt = $conn->prepare("SELECT id_petugas, password, nama_petugas FROM petugas WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // VERIFIKASI PASSWORD HASH
        // Jika di database masih password polos, kode ini akan gagal (makanya tadi kita update SQL)
        if (password_verify($password, $row['password'])) {
            session_start();
            $_SESSION['id_petugas'] = $row['id_petugas'];
            $_SESSION['username'] = $username;
            $_SESSION['login'] = true;

            echo json_encode(["status" => "success", "message" => "Login berhasil!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Password salah!"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Username tidak ditemukan!"]);
    }
    $stmt->close();
    exit;
}
?>