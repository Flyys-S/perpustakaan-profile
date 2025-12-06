<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Ambil data
    $nisn = $_POST['nisn'] ?? ''; // BARU
    $password_raw = $_POST['password'] ?? ''; // BARU
    $nama = $_POST['nama'] ?? '';
    $kelas = $_POST['kelas'] ?? '';
    $alasan = $_POST['alasan'] ?? '';
    
    // Validasi
    if (empty($nisn) || empty($password_raw) || empty($nama) || empty($kelas)) {
        echo json_encode(["status" => "error", "message" => "NISN, Password, Nama, dan Kelas wajib diisi!"]);
        exit;
    }

    // Cek apakah NISN sudah terdaftar
    $cek = $conn->prepare("SELECT id FROM anggota WHERE nisn = ?");
    $cek->bind_param("s", $nisn);
    $cek->execute();
    if ($cek->get_result()->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "NISN ini sudah terdaftar!"]);
        exit;
    }
    
    // Hash Password (Enkripsi Aman)
    $password_hash = password_hash($password_raw, PASSWORD_DEFAULT);

    // Upload Foto
    $foto_db = ''; 
    if (!empty($_FILES['foto']['name'])) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        
        $file_name = time() . "_" . basename($_FILES['foto']['name']);
        $target_file = $target_dir . $file_name;
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        if (in_array($fileType, ['jpg', 'jpeg', 'png']) && move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            $foto_db = $file_name; // Simpan nama filenya saja biar rapi
        }
    }
    
    // Insert ke Database
    $stmt = $conn->prepare("INSERT INTO anggota (nisn, password, nama, kelas, alasan, foto, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("ssssss", $nisn, $password_hash, $nama, $kelas, $alasan, $foto_db);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Pendaftaran berhasil! Silakan login setelah diverifikasi admin."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal: " . $stmt->error]);
    }
    $stmt->close();
    exit;
}
?>