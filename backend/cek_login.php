<?php
// Pastikan output hanya JSON dan bersihkan output yang tidak diinginkan
header('Content-Type: application/json; charset=utf-8');
ob_start();

// Panggil file koneksi database
include 'koneksi.php';

// Cek apakah form disubmit dengan method POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Gunakan mysqli_real_escape_string untuk kompatibilitas
    $username = isset($_POST['username']) ? mysqli_real_escape_string($conn, $_POST['username']) : '';
    $password = isset($_POST['password']) ? mysqli_real_escape_string($conn, $_POST['password']) : '';

    // Validasi input tidak boleh kosong
    if (empty($username) || empty($password)) {
        ob_end_clean();
        echo json_encode(["status" => "error", "message" => "Username dan password harus diisi!"]);
        exit;
    }

    // Query untuk cek username di database
    $query = "SELECT * FROM petugas WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
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

            ob_end_clean();
            echo json_encode(["status" => "success", "message" => "Login berhasil!"]);
        } else {
            // Password salah
            ob_end_clean();
            echo json_encode(["status" => "error", "message" => "Password salah!"]);
        }
    } else {
        // Username tidak ditemukan atau query error
        ob_end_clean();
        echo json_encode(["status" => "error", "message" => "Username tidak ditemukan!"]);
    }
    exit;
}

// Jika bukan POST, kembalikan error JSON
ob_end_clean();
echo json_encode(["status" => "error", "message" => "Metode harus POST."]);
exit;
?>