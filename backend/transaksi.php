<?php
session_start();
include 'koneksi.php';

// === API: AMBIL DATA (GET) ===
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!isset($_SESSION['login'])) { http_response_code(403); exit; }
    
    $filter_status = $_GET['status'] ?? 'dipinjam'; 

    $query = "SELECT t.id, a.nama, a.kelas, b.judul, t.tanggal_pinjam, t.status 
              FROM transaksi t
              JOIN anggota a ON t.id_anggota = a.id
              JOIN buku b ON t.id_buku = b.id
              WHERE t.status = '$filter_status'
              ORDER BY t.tanggal_pinjam DESC";
              
    $result = mysqli_query($conn, $query);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) { $data[] = $row; }
    echo json_encode(["status" => "success", "data" => $data]);
    exit;
}

// === API: PROSES (POST) ===
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $aksi = $_POST['aksi'] ?? ''; 

    // 1. ANGGOTA AJUKAN
    if ($aksi == 'ajukan_pinjam') {
        if (!isset($_SESSION['login_anggota'])) { echo json_encode(["status"=>"error","message"=>"Login dulu"]); exit; }
        $id_anggota = $_SESSION['id_anggota'];
        $id_buku    = $_POST['id_buku'];

        $cek = mysqli_query($conn, "SELECT stok FROM buku WHERE id = '$id_buku'");
        if (mysqli_fetch_assoc($cek)['stok'] <= 0) { echo json_encode(["status"=>"error","message"=>"Stok habis"]); exit; }

        $cekPinjam = mysqli_query($conn, "SELECT id FROM transaksi WHERE id_anggota='$id_anggota' AND id_buku='$id_buku' AND status IN ('pending','disetujui','dipinjam')");
        if (mysqli_num_rows($cekPinjam) > 0) { echo json_encode(["status"=>"error","message"=>"Sudah diajukan/dipinjam"]); exit; }

        $conn->query("INSERT INTO transaksi (id_anggota, id_buku, tanggal_pinjam, status) VALUES ('$id_anggota', '$id_buku', NOW(), 'pending')");
        echo json_encode(["status" => "success", "message" => "Berhasil diajukan!"]);
        exit;
    }

    // PROTEKSI ADMIN
    if (!isset($_SESSION['login'])) exit;

    // 2. ADMIN SETUJUI (Stok Berkurang) -> Status: DISETUJUI (Siap Ambil)
    if ($aksi == 'setujui') {
        $id_transaksi = $_POST['id_transaksi'];
        $q = mysqli_query($conn, "SELECT id_buku FROM transaksi WHERE id='$id_transaksi'");
        $id_buku = mysqli_fetch_assoc($q)['id_buku'];

        mysqli_begin_transaction($conn);
        try {
            $conn->query("UPDATE transaksi SET status = 'disetujui' WHERE id = '$id_transaksi'");
            $conn->query("UPDATE buku SET stok = stok - 1 WHERE id = '$id_buku'");
            mysqli_commit($conn);
            echo json_encode(["status" => "success", "message" => "Disetujui. Menunggu pengambilan."]);
        } catch (Exception $e) { mysqli_rollback($conn); echo json_encode(["status"=>"error"]); }
        exit;
    }

    // 3. ADMIN KONFIRMASI AMBIL -> Status: DIPINJAM
    if ($aksi == 'ambil') {
        $id_transaksi = $_POST['id_transaksi'];
        // Update tanggal pinjam jadi NOW() agar hitungan denda akurat mulai dari saat buku diambil
        $conn->query("UPDATE transaksi SET status = 'dipinjam', tanggal_pinjam = NOW() WHERE id = '$id_transaksi'");
        echo json_encode(["status" => "success", "message" => "Buku telah diambil anggota."]);
        exit;
    }

    // 4. ADMIN TOLAK
    if ($aksi == 'tolak') {
        $id_transaksi = $_POST['id_transaksi'];
        $conn->query("UPDATE transaksi SET status = 'ditolak' WHERE id = '$id_transaksi'");
        echo json_encode(["status" => "success", "message" => "Ditolak."]);
        exit;
    }

    // 5. ADMIN KEMBALIKAN
    if ($aksi == 'kembali') {
        $id_transaksi = $_POST['id_transaksi'];
        $q = mysqli_query($conn, "SELECT id_buku FROM transaksi WHERE id='$id_transaksi'");
        $id_buku = mysqli_fetch_assoc($q)['id_buku'];

        mysqli_begin_transaction($conn);
        try {
            $conn->query("UPDATE transaksi SET status = 'kembali', tanggal_kembali = NOW() WHERE id = '$id_transaksi'");
            $conn->query("UPDATE buku SET stok = stok + 1 WHERE id = '$id_buku'");
            mysqli_commit($conn);
            echo json_encode(["status" => "success", "message" => "Dikembalikan."]);
        } catch (Exception $e) { mysqli_rollback($conn); echo json_encode(["status"=>"error"]); }
        exit;
    }
}
?>