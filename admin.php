<?php
session_start();

// Jika belum login, arahkan ke halaman login
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
    <title>Admin Dashboard & Data - Perpustakaan</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <header class="navbar">
        <div class="container">
            <a href="index.html" class="logo">
                <img src="assets/picture/logo.png" alt="Logo Perpustakaan" class="logo-img">
                Admin Perpustakaan
            </a>

            <nav class="nav-links">
                <a href="#dashboard" class="active">Dashboard</a>
                <a href="#verifikasi-anggota">Verifikasi Anggota</a>
                <a href="#data-anggota">Anggota</a>
                <a href="#data-buku">Buku</a>
                <a href="#">Peminjaman</a>
                <a href="backend/logout.php" class="logout">Logout</a>
            </nav>
            <div class="menu-toggle">
                <button id="theme-toggle" class="theme-button">嫌</button>
                <button class="hamburger" id="hamburger-menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </header>

    <main>
        <section class="container page-section" id="dashboard">
            <h1 class="page-title">Selamat Datang, Petugas!</h1>
            
            <div class="admin-dashboard">
                
                <div class="admin-card">
                    <span class="admin-icon">👥</span>
                    <h2>Total Anggota</h2>
                    <p>850 Orang</p>
                    <a href="#data-anggota" class="cta-button">Lihat Data</a>
                </div>

                <div class="admin-card">
                    <span class="admin-icon">📚</span>
                    <h2>Total Koleksi</h2>
                    <p>4.200 Judul</p>
                    <a href="#data-buku" class="cta-button">Kelola Buku</a>
                </div>

                <div class="admin-card">
                    <span class="admin-icon">📝</span>
                    <h2>Buku Dipinjam</h2>
                    <p>125 Eksemplar</p>
                    <a href="#" class="cta-button">Mulai Transaksi</a>
                </div>

                <div class="admin-card">
                    <span class="admin-icon">💲</span>
                    <h2>Denda Terkumpul</h2>
                    <p>Rp 750.000</p>
                    <a href="#" class="cta-button">Lihat Laporan</a>
                </div>
            </div>
        </section>
        
        <hr class="container page-divider">

        <section class="container page-section pt-0" id="verifikasi-anggota">
            <h2 class="section-title">Verifikasi Pendaftaran Anggota Baru</h2>
            
            <div id="notif-verifikasi" class="form-notif" style="display: none;"></div>

            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>Alasan</th>
                            <th>Foto</th>
                            <th>Tanggal Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tabel-verifikasi-body">
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 20px;">Memuat data pendaftar...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
        </section>

        <hr class="container page-divider">

        <section class="container page-section pt-0" id="data-anggota">
            <h2 class="section-title">Daftar Anggota Perpustakaan</h2>
            
            <div class="form-add-button">
                <a href="form_anggota.html" class="cta-button secondary">Tambah Anggota Baru</a>
            </div>

            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>NIM/NISN</th>
                            <th>Nama Lengkap</th>
                            <th>Kelas/Jabatan</th>
                            <th>Email</th>
                            <th>Tgl. Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tabel-anggota-body">
                        </tbody>
                </table>
            </div>
        </section>
        
        <hr class="container page-divider">

        <section class="container page-section pt-0" id="data-buku">
            <h2 class="section-title">Inventaris Koleksi Buku</h2>

            <div class="form-add-button">
                <a href="form_buku.html" class="cta-button secondary">Tambah Buku Baru</a>
            </div>

            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Kode/ISBN</th>
                            <th>Judul</th>
                            <th>Pengarang</th>
                            <th>Penerbit</th>
                            <th>Tahun</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tabel-buku-body">
                        </tbody>
                </table>
            </div>
            
            <div class="back-to-top-link">
                 <a href="#dashboard" class="cta-button secondary">Kembali ke Atas</a>
            </div>
            
        </section>
    </main>

    <footer class="footer">
        <div class="container footer-center">
            <h3>SMA Negeri 8 Tangerang Selatan</h3>
            <p>Sistem Informasi Perpustakaan | Hak Cipta &copy; 2025</p>
            <p>Didukung oleh: <a href="mailto:info@sma8tangsel.sch.id">info@sma8tangsel.sch.id</a></p>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
    
    <script>
        // === FUNGSI YANG SUDAH ADA (VERIFIKASI) ===

        // Load data pendaftar yang menunggu verifikasi
        function loadVerifikasiAnggota() {
            const tbody = document.getElementById('tabel-verifikasi-body');
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 20px;">Memuat data pendaftar...</td></tr>';
            
            fetch('backend/verifikasi_anggota.php') // Ini adalah request GET
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success' && data.data.length > 0) {
                        tbody.innerHTML = '';
                        data.data.forEach(item => {
                            const row = document.createElement('tr');
                            // Perbaikan path foto: tambahkan "backend/"
                            row.innerHTML = `
                                <td>${item.nama}</td>
                                <td>${item.kelas}</td>
                                <td>${item.alasan}</td>
                                <td>
                                    ${item.foto ? `<a href="backend/${item.foto}" target="_blank">Lihat</a>` : '-'}
                                </td>
                                <td>${item.tanggal_daftar}</td>
                                <td>
                                    <button onclick="verifikasiAnggota(${item.id}, 'terima')" class="cta-button" style="background-color: #28a745;">Terima</button>
                                    <button onclick="verifikasiAnggota(${item.id}, 'tolak')" class="cta-button" style="background-color: #dc3545;">Tolak</button>
                                </td>
                            `;
                            tbody.appendChild(row);
                        });
                    } else {
                        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 20px;">Tidak ada pendaftar yang menunggu verifikasi.</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Error loading verifikasi data:', error);
                    tbody.innerHTML = 
                        '<tr><td colspan="6" style="text-align: center; padding: 20px; color: red;">Gagal memuat data</td></tr>';
                });
        }

        // Function untuk verifikasi anggota (terima/tolak)
        function verifikasiAnggota(id, aksi) {
            const notifDiv = document.getElementById('notif-verifikasi');
            
            if (!confirm(`Apakah Anda yakin ingin ${aksi === 'terima' ? 'menerima' : 'menolak'} pendaftaran ini?`)) {
                return;
            }

            const formData = new FormData();
            formData.append('id', id);
            formData.append('aksi', aksi);

            fetch('backend/verifikasi_anggota.php', { // Ini adalah request POST
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    notifDiv.className = 'form-notif success';
                    notifDiv.textContent = data.message;
                    notifDiv.style.display = 'block';
                    
                    // Reload data setelah 1 detik
                    setTimeout(() => {
                        loadVerifikasiAnggota(); // Muat ulang tabel verifikasi
                        // Perbaikan: Muat ulang juga tabel anggota aktif
                        if (aksi === 'terima') {
                            loadDataAnggota(); 
                        }
                        notifDiv.style.display = 'none';
                    }, 1500);
                } else {
                    notifDiv.className = 'form-notif error';
                    notifDiv.textContent = data.message;
                    notifDiv.style.display = 'block';
                }
            })
            .catch(error => {
                notifDiv.className = 'form-notf error';
                notifDiv.textContent = 'Terjadi kesalahan: ' + error;
                notifDiv.style.display = 'block';
            });
        }

        // === FUNGSI BARU UNTUK MEMUAT TABEL ANGGOTA AKTIF ===
        function loadDataAnggota() {
            const tbody = document.getElementById('tabel-anggota-body');
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 20px;">Memuat data anggota...</td></tr>';
            
            fetch('backend/anggota.php') // Memanggil endpoint anggota
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success' && data.data.length > 0) {
                        tbody.innerHTML = ''; // Kosongkan
                        data.data.forEach(item => {
                            const row = document.createElement('tr');
                            // Sesuaikan kolom ini dengan struktur tabel 'anggota' Anda
                            row.innerHTML = `
                                <td>${item.id}</td> 
                                <td>${item.nama}</td>
                                <td>${item.kelas}</td>
                                <td>${item.email || '-'}</td>
                                <td>${item.tanggal_bergabung || 'N/A'}</td>
                                <td><a href="#">Edit</a> | <a href="#">Hapus</a></td>
                            `;
                            tbody.appendChild(row);
                        });
                    } else if (data.status === 'success') {
                        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 20px;">Belum ada anggota yang terdaftar.</td></tr>';
                    } else {
                        tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; padding: 20px; color: red;">${data.message}</td></tr>`;
                    }
                })
                .catch(error => {
                    console.error('Error loading data anggota:', error);
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 20px; color: red;">Gagal memuat data anggota</td></tr>';
                });
        }

        // === FUNGSI BARU UNTUK MEMUAT TABEL BUKU ===
        function loadDataBuku() {
            const tbody = document.getElementById('tabel-buku-body');
            tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px;">Memuat data buku...</td></tr>';
            
            fetch('backend/api.php') // Memanggil endpoint buku (GET)
                .then(response => response.json())
                .then(data => {
                    // Endpoint api.php Anda mengembalikan array langsung
                    if (Array.isArray(data) && data.length > 0) {
                        tbody.innerHTML = ''; // Kosongkan
                        data.forEach(item => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${item.isbn || item.nomor_buku}</td>
                                <td>${item.judul}</td>
                                <td>${item.pengarang}</td>
                                <td>${item.penerbit}</td>
                                <td>${item.tahun_terbit}</td>
                                <td>${item.stok || '1'}</td>
                                <td><a href="#">Edit</a> | <a href="#">Hapus</a></td>
                            `;
                            tbody.appendChild(row);
                        });
                    } else if (Array.isArray(data)) {
                        tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px;">Belum ada data buku di inventaris.</td></tr>';
                    } else {
                        tbody.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 20px; color: red;">Format data salah</td></tr>`;
                    }
                })
                .catch(error => {
                    console.error('Error loading data buku:', error);
                    tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px; color: red;">Gagal memuat data buku</td></tr>';
                });
        }


        // Load semua data saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            loadVerifikasiAnggota(); // Memuat data verifikasi
            loadDataAnggota();       // Memuat data anggota
            loadDataBuku();          // Memuat data buku
        });
    </script>
    
</body>
</html>