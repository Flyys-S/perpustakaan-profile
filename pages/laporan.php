<?php
session_start();

// 1. Cek Login Admin
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.html');
    exit;
}

// 2. Include Koneksi
include '../backend/koneksi.php';

// === PERBAIKAN PENTING: Paksa jadi HTML ===
header("Content-Type: text/html; charset=UTF-8");
// ==========================================
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Perpustakaan</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid black; padding-bottom: 10px; }
        .header h1 { margin: 0; }
        .header p { margin: 5px 0; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        
        .status-dipinjam { color: orange; font-weight: bold; }
        .status-kembali { color: green; font-weight: bold; }

        /* Sembunyikan tombol saat dicetak */
        @media print {
            .no-print { display: none; }
        }
        
        .btn-print {
            background: #007bff; color: white; padding: 10px 20px; 
            border: none; cursor: pointer; border-radius: 5px; text-decoration: none;
            display: inline-block; margin-bottom: 20px;
        }
        .btn-back {
            background: #6c757d; color: white; padding: 10px 20px; 
            border: none; cursor: pointer; border-radius: 5px; text-decoration: none;
            display: inline-block; margin-right: 10px;
        }
    </style>
</head>
<body>

    <div class="no-print">
        <a href="admin.php" class="btn-back">← Kembali ke Dashboard</a>
        <button onclick="window.print()" class="btn-print">🖨️ Cetak Laporan (PDF)</button>
    </div>

    <div class="header">
        <img src="../assets/picture/logo.png" style="width: 80px; height: auto; float: left;">
        <h1>PERPUSTAKAAN SMAN 8 TANGERANG SELATAN</h1>
        <p>Jl. Cireundeu Raya No. 5 Ciputat Timur</p>
        <p>Laporan Sirkulasi Peminjaman Buku</p>
        <div style="clear: both;"></div>
    </div>

    <p><strong>Tanggal Cetak:</strong> <?= date('d F Y') ?></p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Peminjam</th>
                <th>Kelas</th>
                <th>Judul Buku</th>
                <th>Tgl Pinjam</th>
                <th>Tgl Kembali</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Ambil semua data transaksi (History Lengkap)
            $query = "SELECT t.*, a.nama, a.kelas, b.judul 
                      FROM transaksi t
                      JOIN anggota a ON t.id_anggota = a.id
                      JOIN buku b ON t.id_buku = b.id
                      ORDER BY t.tanggal_pinjam DESC";
            
            $result = mysqli_query($conn, $query);
            $no = 1;
            
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $statusClass = ($row['status'] == 'dipinjam') ? 'status-dipinjam' : 'status-kembali';
                    
                    // Format tanggal agar lebih enak dibaca (dd-mm-yyyy)
                    $tglPinjam = date('d-m-Y', strtotime($row['tanggal_pinjam']));
                    $tglKembali = $row['tanggal_kembali'] ? date('d-m-Y', strtotime($row['tanggal_kembali'])) : '-';
                    
                    echo "<tr>
                        <td>{$no}</td>
                        <td>{$row['nama']}</td>
                        <td>{$row['kelas']}</td>
                        <td>{$row['judul']}</td>
                        <td>{$tglPinjam}</td>
                        <td>{$tglKembali}</td>
                        <td class='{$statusClass}'>" . strtoupper($row['status']) . "</td>
                    </tr>";
                    $no++;
                }
            } else {
                echo "<tr><td colspan='7' style='text-align:center'>Belum ada riwayat transaksi.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div style="margin-top: 50px; float: right; text-align: center; width: 200px;">
        <p>Tangerang Selatan, <?= date('d F Y') ?></p>
        <br><br><br>
        <p>( Petugas Perpustakaan )</p>
    </div>

</body>
</html>