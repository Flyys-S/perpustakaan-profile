<?php
session_start();
if (!isset($_SESSION['login_anggota'])) { header('Location: login_anggota.html'); exit; }
include '../backend/koneksi.php';
header("Content-Type: text/html; charset=UTF-8"); 

$id = $_SESSION['id_anggota'];

// Data Diri
$stmt = $conn->prepare("SELECT * FROM anggota WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$member = $stmt->get_result()->fetch_assoc();

// Transaksi (Pending, Disetujui, Dipinjam)
$stmt_trx = $conn->prepare("
    SELECT b.judul, t.tanggal_pinjam, t.status 
    FROM transaksi t 
    JOIN buku b ON t.id_buku = b.id 
    WHERE t.id_anggota = ? AND t.status IN ('pending', 'disetujui', 'dipinjam')
    ORDER BY t.tanggal_pinjam DESC
");
$stmt_trx->bind_param("i", $id);
$stmt_trx->execute();
$transaksi = $stmt_trx->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Anggota</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* CSS MEMBER */
        .profile-card { background: var(--bg-secondary); padding: 2rem; border-radius: 12px; box-shadow: var(--shadow); display: flex; align-items: center; gap: 2rem; max-width: 800px; margin: 0 auto; }
        .profile-img { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary-color); }
        .profile-info h2 { margin-bottom: 0.5rem; color: var(--primary-color); }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; color: white; }
        
        /* Status Badges */
        .status-pending { background: #6c757d; } 
        .status-disetujui { background: #28a745; } /* Hijau: Siap Ambil */
        .status-dipinjam { background: #ffc107; color: #000; } /* Kuning: Sedang Dibawa */
        .status-active { background: #007bff; }

        .search-container { position: relative; max-width: 500px; margin: 0 auto 30px auto; }
        .search-input { width: 100%; padding: 15px 20px; border-radius: 30px; border: 1px solid var(--border-color); font-size: 1rem; background-color: var(--bg-secondary); box-shadow: var(--shadow); }
        
        .book-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 25px; padding: 10px 0; }
        .book-card { background-color: var(--bg-secondary); border-radius: 12px; padding: 15px; box-shadow: var(--shadow); transition: transform 0.3s ease; position: relative; cursor: pointer; display: flex; flex-direction: column; }
        .book-card:hover { transform: translateY(-8px); }
        .book-cover { width: 100%; aspect-ratio: 2/3; object-fit: cover; border-radius: 8px; margin-bottom: 12px; background-color: #eee; }
        .book-title { font-weight: 700; font-size: 1rem; margin-bottom: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .book-meta { margin-top: auto; display: flex; justify-content: space-between; font-size: 0.8rem; }
        
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 2000; display: none; justify-content: center; align-items: center; }
        .modal-overlay.active { display: flex; }
        .modal-box { background: var(--bg-secondary); width: 90%; max-width: 700px; border-radius: 15px; overflow: hidden; display: flex; }
        .modal-img-col { width: 40%; background: #f0f0f0; display: flex; align-items: center; justify-content: center; }
        .modal-img { width: 100%; height: 100%; object-fit: cover; }
        .modal-info-col { width: 60%; padding: 30px; position: relative; }
        .close-modal { position: absolute; top: 15px; right: 20px; font-size: 1.5rem; cursor: pointer; }
        .btn-pinjam { width: 100%; padding: 12px; background: var(--primary-color); color: white; border: none; border-radius: 8px; font-size: 1rem; cursor: pointer; margin-top: 20px; }
        .btn-pinjam:disabled { background: #ccc; cursor: not-allowed; }
        
        .msg-alert { font-size: 0.85rem; font-weight: 600; margin-top: 5px; display: block; }
        @media (max-width: 768px) { .profile-card, .modal-box { flex-direction: column; text-align: center; } .modal-img-col, .modal-info-col { width: 100%; } }
    </style>
</head>
<body>

    <header class="navbar">
        <div class="container">
            <a href="index.html" class="logo"><img src="../assets/picture/logo.png" class="logo-img"> Area Anggota</a>
            <nav class="nav-links">
                <a href="#profil" class="active">Profil</a>
                <a href="#status-pinjaman">Status Pinjaman</a>
                <a href="#koleksi">Koleksi</a>
                <a href="../backend/logout.php" class="logout">Logout</a>
            </nav>
             <div class="menu-toggle"><button id="theme-toggle" class="theme-button">🌙</button></div>
        </div>
    </header>

    <main class="page-section" id="profil">
        <div class="container">
            <h1 class="page-title">Halo, <?= htmlspecialchars($member['nama']) ?>! 👋</h1>
            
            <div class="profile-card">
                <?php $foto = $member['foto'] ? "../backend/uploads/" . $member['foto'] : "../assets/picture/logo.png"; ?>
                <img src="<?= $foto ?>" class="profile-img">
                <div class="profile-info">
                    <h2><?= htmlspecialchars($member['nama']) ?></h2>
                    <p>NISN: <?= htmlspecialchars($member['nisn']) ?></p>
                    <span class="badge status-active">ANGGOTA AKTIF</span>
                </div>
            </div>

            <hr class="page-divider" id="status-pinjaman">

            <h2 class="section-title">📋 Status Peminjaman</h2>
            <div class="data-table-container">
                <table class="data-table">
                    <thead><tr><th>Judul Buku</th><th>Tanggal Request</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php if ($transaksi->num_rows > 0): ?>
                            <?php while($row = $transaksi->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['judul']) ?></td>
                                <td><?= date('d M Y', strtotime($row['tanggal_pinjam'])) ?></td>
                                <td>
                                    <?php if($row['status'] == 'pending'): ?>
                                        <span class="badge status-pending">⏳ Menunggu Konfirmasi</span>
                                    
                                    <?php elseif($row['status'] == 'disetujui'): ?>
                                        <span class="badge status-disetujui">✅ Disetujui</span>
                                        <span class="msg-alert" style="color: #28a745;">📍 Silakan ambil buku di perpustakaan</span>
                                    
                                    <?php elseif($row['status'] == 'dipinjam'): ?>
                                        <span class="badge status-dipinjam">📖 Sedang Dipinjam</span>
                                        <span class="msg-alert" style="color: #856404;">⚠️ Sudah diambil, harap kembalikan tepat waktu</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3" style="text-align:center; padding:15px;">Belum ada aktivitas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <hr class="page-divider" id="koleksi">

            <h2 class="section-title">📖 Jelajahi Koleksi</h2>
            <div class="search-container"><input type="text" id="cariBuku" class="search-input" placeholder="🔍 Cari judul buku..."></div>
            <div id="book-grid-container" class="book-grid"><p style="text-align:center;">Memuat...</p></div>
        </div>
    </main>

    <div class="modal-overlay" id="detailModal">
        <div class="modal-box">
            <div class="modal-img-col"><img src="" id="modalImg" class="modal-img"></div>
            <div class="modal-info-col">
                <span class="close-modal" onclick="closeModal()">&times;</span>
                <h2 id="modalTitle">Judul</h2>
                <p id="modalMeta">Meta</p>
                <div style="margin-top:15px;"><p>Stok: <span id="modalStok" style="font-weight:bold;">0</span></p></div>
                <button id="btnAjukan" class="btn-pinjam">Ajukan Pinjaman</button>
            </div>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
    <script>
        let currentBookId = null;
        const modal = document.getElementById('detailModal');
        const btnAjukan = document.getElementById('btnAjukan');

        function loadKoleksi() {
            fetch('../backend/api.php').then(res=>res.json()).then(data=>{
                const container = document.getElementById('book-grid-container');
                container.innerHTML = '';
                if(data.length > 0) {
                    data.forEach(item => {
                        const cover = item.cover ? `../backend/uploads/${item.cover}` : `../assets/picture/logo.png`;
                        const card = document.createElement('div');
                        card.className = 'book-card';
                        card.dataset.search = (item.judul + item.pengarang).toLowerCase();
                        card.onclick = () => openModal(item);
                        card.innerHTML = `<img src="${cover}" class="book-cover">
                                          <div class="book-title">${item.judul}</div>
                                          <div class="book-author">${item.pengarang||'-'}</div>
                                          <div class="book-meta"><span>${item.tahun_terbit}</span><span>Stok: ${item.stok}</span></div>`;
                        container.appendChild(card);
                    });
                } else container.innerHTML = 'Kosong';
            });
        }

        function openModal(item) {
            currentBookId = item.id;
            document.getElementById('modalTitle').innerText = item.judul;
            document.getElementById('modalMeta').innerText = item.pengarang;
            document.getElementById('modalStok').innerText = item.stok;
            document.getElementById('modalImg').src = item.cover ? `../backend/uploads/${item.cover}` : `../assets/picture/logo.png`;
            if(item.stok > 0) {
                btnAjukan.disabled = false; btnAjukan.innerText = "Ajukan Pinjaman"; btnAjukan.style.background = "var(--primary-color)";
            } else {
                btnAjukan.disabled = true; btnAjukan.innerText = "Stok Habis"; btnAjukan.style.background = "#ccc";
            }
            modal.classList.add('active');
        }
        function closeModal() { modal.classList.remove('active'); }
        modal.onclick = (e) => { if(e.target===modal) closeModal(); }

        btnAjukan.onclick = () => {
            if(!confirm("Ajukan peminjaman?")) return;
            const fd = new FormData(); fd.append('aksi', 'ajukan_pinjam'); fd.append('id_buku', currentBookId);
            fetch('../backend/transaksi.php', { method: 'POST', body: fd }).then(res=>res.json()).then(data=>{
                alert(data.message); closeModal(); location.reload(); 
            });
        };

        document.getElementById('cariBuku').addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            document.querySelectorAll('.book-card').forEach(c => {
                c.style.display = c.dataset.search.includes(filter) ? 'flex' : 'none';
            });
        });

        document.addEventListener('DOMContentLoaded', loadKoleksi);
    </script>
</body>
</html>