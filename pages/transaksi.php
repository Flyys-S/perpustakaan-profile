<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) { header('Location: login.html'); exit; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transaksi - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <header class="navbar">
        <div class="container">
            <a href="index.html" class="logo"><img src="../assets/picture/logo.png" class="logo-img"> Admin Transaksi</a>
            <nav class="nav-links">
                <a href="admin.php">Dashboard</a>
                <a href="transaksi.php" class="active">Transaksi</a>
                <a href="../backend/logout.php" class="logout">Logout</a>
            </nav>
        </div>
    </header>

    <main class="page-section">
        <div class="container">
            <h1 class="page-title">Sirkulasi Perpustakaan</h1>

            <h2 class="section-title">1️⃣ Permintaan Baru</h2>
            <div class="data-table-container">
                <table class="data-table">
                    <thead><tr><th>Nama</th><th>Buku</th><th>Tgl Request</th><th>Aksi</th></tr></thead>
                    <tbody id="tabel-pending"><tr><td colspan="4" style="text-align:center;">Memuat...</td></tr></tbody>
                </table>
            </div>

            <hr class="page-divider">

            <h2 class="section-title">2️⃣ Siap Diambil (Disetujui)</h2>
            <div class="data-table-container">
                <table class="data-table">
                    <thead><tr><th>Nama</th><th>Buku</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody id="tabel-disetujui"><tr><td colspan="4" style="text-align:center;">Memuat...</td></tr></tbody>
                </table>
            </div>

            <hr class="page-divider">

            <h2 class="section-title">3️⃣ Sedang Dipinjam</h2>
            <div class="data-table-container">
                <table class="data-table">
                    <thead><tr><th>Nama</th><th>Buku</th><th>Tgl Ambil</th><th>Tenggat</th><th>Aksi</th></tr></thead>
                    <tbody id="tabel-dipinjam"><tr><td colspan="5" style="text-align:center;">Memuat...</td></tr></tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="../assets/js/main.js"></script>
    <script>
        // 1. Load Pending
        function loadPending() {
            fetch('../backend/transaksi.php?status=pending').then(res=>res.json()).then(data=>{
                const tbody = document.getElementById('tabel-pending');
                tbody.innerHTML = '';
                if(data.data.length > 0) {
                    data.data.forEach(item => {
                        tbody.innerHTML += `<tr><td>${item.nama}</td><td>${item.judul}</td><td>${item.tanggal_pinjam}</td>
                        <td><button onclick="aksiAdmin(${item.id}, 'setujui')" class="cta-button" style="padding:5px 10px; background:#28a745;">Setujui</button> <button onclick="aksiAdmin(${item.id}, 'tolak')" class="cta-button" style="padding:5px 10px; background:#dc3545;">Tolak</button></td></tr>`;
                    });
                } else tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;">Tidak ada permintaan.</td></tr>';
            });
        }

        // 2. Load Disetujui (Siap Ambil)
        function loadDisetujui() {
            fetch('../backend/transaksi.php?status=disetujui').then(res=>res.json()).then(data=>{
                const tbody = document.getElementById('tabel-disetujui');
                tbody.innerHTML = '';
                if(data.data.length > 0) {
                    data.data.forEach(item => {
                        tbody.innerHTML += `<tr><td>${item.nama}</td><td>${item.judul}</td>
                        <td><span style="color:blue; font-weight:bold;">Menunggu Diambil</span></td>
                        <td><button onclick="aksiAdmin(${item.id}, 'ambil')" class="cta-button" style="padding:5px 10px; background:#007bff;">📦 Konfirmasi Ambil</button></td></tr>`;
                    });
                } else tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;">Tidak ada buku menunggu diambil.</td></tr>';
            });
        }

        // 3. Load Dipinjam
        function loadDipinjam() {
            fetch('../backend/transaksi.php?status=dipinjam').then(res=>res.json()).then(data=>{
                const tbody = document.getElementById('tabel-dipinjam');
                tbody.innerHTML = '';
                if(data.data.length > 0) {
                    data.data.forEach(item => {
                        // Hitung tenggat (sederhana di JS)
                        let tgl = new Date(item.tanggal_pinjam);
                        tgl.setDate(tgl.getDate() + 7);
                        let tenggat = tgl.toISOString().split('T')[0];

                        tbody.innerHTML += `<tr><td>${item.nama}</td><td>${item.judul}</td><td>${item.tanggal_pinjam}</td><td>${tenggat}</td>
                        <td><button onclick="aksiAdmin(${item.id}, 'kembali')" class="cta-button" style="padding:5px 10px;">✅ Kembalikan</button></td></tr>`;
                    });
                } else tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Tidak ada buku dipinjam.</td></tr>';
            });
        }

        function aksiAdmin(id, aksi) {
            if(!confirm(`Lanjutkan proses ${aksi}?`)) return;
            const fd = new FormData(); fd.append('aksi', aksi); fd.append('id_transaksi', id);
            fetch('../backend/transaksi.php', { method: 'POST', body: fd }).then(res=>res.json()).then(data=>{
                alert(data.message);
                loadPending(); loadDisetujui(); loadDipinjam();
            });
        }

        document.addEventListener('DOMContentLoaded', () => { loadPending(); loadDisetujui(); loadDipinjam(); });
    </script>
</body>
</html>