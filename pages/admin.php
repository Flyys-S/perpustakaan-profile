<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <style>
        .search-box { margin-bottom: 15px; display: flex; gap: 10px; }
        .search-input { width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 1rem; transition: border-color 0.3s; }
        .search-input:focus { border-color: var(--primary-color); outline: none; box-shadow: 0 0 0 3px rgba(0,123,255,0.1); }
        
        .dashboard-number { font-size: 2.5rem; font-weight: bold; color: var(--primary-color); margin: 10px 0; }
        
        /* STYLE BARU: MINI STATS UTK KARTU TRANSAKSI */
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            margin: 15px 0;
            text-align: center;
        }
        .stat-item {
            background: rgba(0,0,0,0.03);
            padding: 8px;
            border-radius: 8px;
        }
        .stat-val {
            font-size: 1.4rem;
            font-weight: bold;
            display: block;
            line-height: 1;
            margin-bottom: 3px;
        }
        .stat-label { font-size: 0.75rem; color: #666; }
        
        /* Warna status */
        .color-pending { color: #d9534f; } /* Merah/Orange */
        .color-siap { color: #28a745; }    /* Hijau */
        .color-pinjam { color: #f0ad4e; }  /* Kuning */
    </style>
</head>
<body>

    <header class="navbar">
        <div class="container">
            <a href="index.html" class="logo"><img src="../assets/picture/logo.png" class="logo-img"> Admin Perpustakaan</a>
            <nav class="nav-links">
                <a href="#dashboard" class="active">Dashboard</a>
                <a href="#verifikasi-anggota">Verifikasi</a>
                <a href="#data-anggota">Anggota</a>
                <a href="#data-buku">Buku</a>
                <a href="../backend/logout.php" class="logout">Logout</a>
            </nav>
            <div class="menu-toggle"><button id="theme-toggle" class="theme-button">🌙</button></div>
        </div>
    </header>

    <main>
        <section class="container page-section" id="dashboard">
            <h1 class="page-title">Selamat Datang, Petugas!</h1>
            
            <div class="admin-dashboard">
                <div class="admin-card">
                    <span class="admin-icon">👥</span>
                    <h2>Total Anggota</h2>
                    <div id="count-anggota" class="dashboard-number">...</div>
                    <p>Anggota Aktif</p>
                    <a href="#data-anggota" class="cta-button">Lihat Data</a>
                </div>

                <div class="admin-card">
                    <span class="admin-icon">📚</span>
                    <h2>Total Koleksi</h2>
                    <div id="count-buku" class="dashboard-number">...</div>
                    <p>Judul Buku</p>
                    <a href="#data-buku" class="cta-button">Kelola Buku</a>
                </div>

                <div class="admin-card">
                    <span class="admin-icon">🔄</span>
                    <h2>Sirkulasi</h2>
                    
                    <div class="stats-grid">
                        <div class="stat-item">
                            <span id="trx-pending" class="stat-val color-pending">0</span>
                            <span class="stat-label">Request</span>
                        </div>
                        <div class="stat-item">
                            <span id="trx-siap" class="stat-val color-siap">0</span>
                            <span class="stat-label">Siap Ambil</span>
                        </div>
                        <div class="stat-item">
                            <span id="trx-dipinjam" class="stat-val color-pinjam">0</span>
                            <span class="stat-label">Dipinjam</span>
                        </div>
                    </div>

                    <a href="transaksi.php" class="cta-button">Kelola Transaksi</a>
                </div>

                <div class="admin-card">
                    <span class="admin-icon">📊</span>
                    <h2>Laporan</h2>
                    <div class="dashboard-number" style="font-size:1.5rem; margin: 25px 0;">Cetak Data</div>
                    <a href="laporan.php" class="cta-button">Lihat Laporan</a>
                </div>

                <div class="admin-card">
                    <span class="admin-icon">📰</span>
                    <h2>Berita & Acara</h2>
                    <p>Update Info</p>
                    <a href="form_berita.html" class="cta-button">Tulis Berita</a>
                </div>
            </div>
        </section>
        
        <hr class="container page-divider">

        <section class="container page-section pt-0" id="verifikasi-anggota">
            <h2 class="section-title">Verifikasi Pendaftaran Anggota Baru</h2>
            <div id="notif-verifikasi" class="form-notif" style="display: none;"></div>
            <div class="data-table-container">
                <table class="data-table">
                    <thead><tr><th>Nama</th><th>Kelas</th><th>Alasan</th><th>Foto</th><th>Tanggal</th><th>Aksi</th></tr></thead>
                    <tbody id="tabel-verifikasi-body"><tr><td colspan="6" style="text-align:center;">Memuat...</td></tr></tbody>
                </table>
            </div>
        </section>

        <hr class="container page-divider">

        <section class="container page-section pt-0" id="data-anggota">
            <h2 class="section-title">Daftar Anggota</h2>
            <div class="form-add-button"><a href="form_anggota.html" class="cta-button secondary">Tambah Anggota</a></div>
            <div class="search-box"><input type="text" id="cariAnggota" class="search-input" placeholder="🔍 Cari anggota..."></div>
            <div class="data-table-container">
                <table class="data-table">
                    <thead><tr><th>ID</th><th>Nama</th><th>Kelas</th><th>Status</th><th>Tanggal</th><th>Aksi</th></tr></thead>
                    <tbody id="tabel-anggota-body"></tbody>
                </table>
            </div>
            <p id="msg-anggota-kosong" style="display:none; text-align:center; color:red;">Tidak ditemukan.</p>
        </section>
        
        <hr class="container page-divider">

        <section class="container page-section pt-0" id="data-buku">
            <h2 class="section-title">Koleksi Buku</h2>
            <div class="form-add-button"><a href="form_buku.html" class="cta-button secondary">Tambah Buku</a></div>
            <div class="search-box"><input type="text" id="cariBuku" class="search-input" placeholder="🔍 Cari buku..."></div>
            <div class="data-table-container">
                <table class="data-table">
                    <thead><tr><th>ISBN</th><th>Judul</th><th>Pengarang</th><th>Penerbit</th><th>Tahun</th><th>Stok</th><th>Aksi</th></tr></thead>
                    <tbody id="tabel-buku-body"></tbody>
                </table>
            </div>
            <p id="msg-buku-kosong" style="display:none; text-align:center; color:red;">Tidak ditemukan.</p>
            <div class="back-to-top-link"><a href="#dashboard" class="cta-button secondary">Kembali ke Atas</a></div>
        </section>
    </main>

    <footer class="footer">
        <div class="container footer-center">
            <h3>SMA Negeri 8 Tangerang Selatan</h3>
            <p>Sistem Informasi Perpustakaan | Hak Cipta &copy; 2025</p>
        </div>
    </footer>

    <script src="../assets/js/main.js"></script>
    <script>
        // === 1. LOAD DASHBOARD STATS (UPDATED) ===
        function loadDashboardStats() {
            fetch('../backend/dashboard_data.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        document.getElementById('count-anggota').innerText = data.total_anggota;
                        document.getElementById('count-buku').innerText = data.total_buku;
                        
                        // Update 3 Angka Transaksi
                        document.getElementById('trx-pending').innerText = data.trx_pending;
                        document.getElementById('trx-siap').innerText = data.trx_siap;
                        document.getElementById('trx-dipinjam').innerText = data.trx_dipinjam;
                    }
                });
        }

        // === 2. FUNGSI LOAD DATA TABEL (SAMA) ===
        function loadVerifikasiAnggota() {
            const tbody = document.getElementById('tabel-verifikasi-body');
            fetch('../backend/verifikasi_anggota.php').then(res => res.json()).then(data => {
                if (data.status === 'success' && data.data.length > 0) {
                    tbody.innerHTML = '';
                    data.data.forEach(item => {
                        const fotoLink = item.foto ? `<a href="../backend/uploads/${item.foto}" target="_blank">Lihat</a>` : '-';
                        tbody.innerHTML += `<tr><td>${item.nama}</td><td>${item.kelas}</td><td>${item.alasan}</td><td>${fotoLink}</td><td>${item.tanggal_daftar}</td>
                        <td><button onclick="verifikasiAnggota(${item.id}, 'terima')" class="cta-button" style="background:#28a745; padding:5px 10px;">Terima</button> 
                        <button onclick="verifikasiAnggota(${item.id}, 'tolak')" class="cta-button" style="background:#dc3545; padding:5px 10px;">Tolak</button></td></tr>`;
                    });
                } else tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:20px;">Tidak ada data.</td></tr>';
            });
        }

        function verifikasiAnggota(id, aksi) {
            if (!confirm(`Yakin ingin ${aksi}?`)) return;
            const fd = new FormData(); fd.append('id', id); fd.append('aksi', aksi);
            fetch('../backend/verifikasi_anggota.php', { method: 'POST', body: fd }).then(res => res.json()).then(data => {
                alert(data.message); loadVerifikasiAnggota(); loadDataAnggota(); loadDashboardStats();
            });
        }

        function loadDataAnggota() {
            fetch('../backend/anggota.php').then(res => res.json()).then(data => {
                const tbody = document.getElementById('tabel-anggota-body');
                if (data.status === 'success' && data.data.length > 0) {
                    tbody.innerHTML = '';
                    data.data.forEach(item => {
                        const tgl = item.tanggal_verifikasi || item.tanggal_daftar;
                        tbody.innerHTML += `<tr><td>${item.id}</td><td>${item.nama}</td><td>${item.kelas}</td><td><span style="color:green; font-weight:bold;">${item.status}</span></td><td>${tgl}</td>
                        <td><a href="edit_anggota.html?id=${item.id}" style="color:blue;">Edit</a> | <a href="#" onclick="hapusAnggota(${item.id})" style="color:red;">Hapus</a></td></tr>`;
                    });
                } else tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:20px;">Kosong.</td></tr>';
            });
        }

        function loadDataBuku() {
            fetch('../backend/api.php').then(res => res.json()).then(data => {
                const tbody = document.getElementById('tabel-buku-body');
                if (Array.isArray(data) && data.length > 0) {
                    tbody.innerHTML = '';
                    data.forEach(item => {
                        tbody.innerHTML += `<tr><td>${item.isbn||item.nomor_buku}</td><td>${item.judul}</td><td>${item.pengarang}</td><td>${item.penerbit}</td><td>${item.tahun_terbit}</td><td>${item.stok}</td>
                        <td><a href="edit_buku.html?id=${item.id}" style="color:blue;">Edit</a> | <a href="#" onclick="hapusBuku(${item.id})" style="color:red;">Hapus</a></td></tr>`;
                    });
                } else tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; padding:20px;">Kosong.</td></tr>';
            });
        }

        function hapusAnggota(id) {
            if(!confirm("Hapus anggota ini?")) return;
            const fd = new FormData(); fd.append('aksi', 'hapus'); fd.append('id', id);
            fetch('../backend/anggota.php', {method:'POST', body:fd}).then(res=>res.json()).then(d=>{alert(d.message); loadDataAnggota(); loadDashboardStats();});
        }
        function hapusBuku(id) {
            if(!confirm("Hapus buku ini?")) return;
            const fd = new FormData(); fd.append('aksi', 'hapus'); fd.append('id', id);
            fetch('../backend/api.php', {method:'POST', body:fd}).then(res=>res.json()).then(d=>{alert(d.message); loadDataBuku(); loadDashboardStats();});
        }

        function setupTableSearch(inputId, tableBodyId, msgId) {
            document.getElementById(inputId).addEventListener('keyup', function() {
                const filter = this.value.toLowerCase();
                const rows = document.getElementById(tableBodyId).getElementsByTagName('tr');
                let found = false;
                for(let row of rows) {
                    if(row.textContent.toLowerCase().includes(filter)) { row.style.display=""; found=true; } else row.style.display="none";
                }
                document.getElementById(msgId).style.display = found ? "none" : "block";
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadDashboardStats(); loadVerifikasiAnggota(); loadDataAnggota(); loadDataBuku();
            setupTableSearch('cariAnggota', 'tabel-anggota-body', 'msg-anggota-kosong');
            setupTableSearch('cariBuku', 'tabel-buku-body', 'msg-buku-kosong');
        });
    </script>
</body>
</html>