<?php
// Panggil file koneksi database
include 'koneksi.php';

// Cek apakah form disubmit dengan method POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_escape_string($conn, $_POST['username']);
    $password = mysqli_escape_string($conn, $_POST['password']);

    // Validasi input tidak boleh kosong
    if (empty($username) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Username dan password harus diisi!"]);
        exit;
    }

    // Query untuk cek username di database
    $query = "SELECT * FROM petugas WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        
        // Verifikasi password (gunakan password_verify jika password tersimpan dengan hash)
        // Untuk sekarang menggunakan perbandingan langsung
        if ($password == $row['password']) {
            // Login berhasil
            session_start();
            $_SESSION['id_petugas'] = $row['id_petugas'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['nama_petugas'] = $row['nama_petugas'];
            $_SESSION['login'] = true;

            echo json_encode(["status" => "success", "message" => "Login berhasil!"]);
        } else {
            // Password salah
            echo json_encode(["status" => "error", "message" => "Password salah!"]);
        }
    } else {
        // Username tidak ditemukan
        echo json_encode(["status" => "error", "message" => "Username tidak ditemukan!"]);
    }
    exit;
}
?>