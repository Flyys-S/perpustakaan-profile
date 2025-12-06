<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nisn = $_POST['nisn'] ?? '';
    $password = $_POST['password'] ?? '';

    // Cek Data Anggota
    $stmt = $conn->prepare("SELECT * FROM anggota WHERE nisn = ?");
    $stmt->bind_param("s", $nisn);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Cek Password
        if (password_verify($password, $row['password'])) {
            
            // Cek Status: Hanya boleh login jika ACTIVE
            if ($row['status'] == 'pending') {
                echo json_encode(["status" => "error", "message" => "Akun Anda masih menunggu verifikasi admin."]);
                exit;
            }
            if ($row['status'] == 'rejected') {
                echo json_encode(["status" => "error", "message" => "Maaf, pendaftaran Anda ditolak."]);
                exit;
            }

            // Login Sukses
            session_start();
            $_SESSION['id_anggota'] = $row['id'];
            $_SESSION['nama_anggota'] = $row['nama'];
            $_SESSION['role'] = 'anggota'; // Pembeda dengan admin
            $_SESSION['login_anggota'] = true;

            echo json_encode(["status" => "success", "message" => "Login berhasil!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Password salah!"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "NISN tidak ditemukan!"]);
    }
    exit;
}
?>