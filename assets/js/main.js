// Menunggu sampai semua konten HTML dimuat
document.addEventListener("DOMContentLoaded", function() {

    // === 1. FUNGSI MOBILE MENU (HAMBURGER) ===
    const hamburgerMenu = document.getElementById('hamburger-menu');
    const navLinks = document.querySelector('.nav-links');

    if (hamburgerMenu) {
        hamburgerMenu.addEventListener('click', function() {
            // Toggle class 'active' pada navLinks dan hamburger
            navLinks.classList.toggle('active');
            hamburgerMenu.classList.toggle('active');
        });
    }

    // === 2. FUNGSI LIGHT/DARK MODE ===
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;

    // Fungsi untuk menerapkan tema
    function applyTheme(theme) {
        if (theme === 'dark') {
            body.classList.add('dark-mode');
            themeToggle.textContent = '☀️'; // Icon matahari
        } else {
            body.classList.remove('dark-mode');
            themeToggle.textContent = '🌙'; // Icon bulan
        }
    }

    // Cek tema yang tersimpan di localStorage
    // '|| "light"' artinya jika tidak ada yg tersimpan, gunakan 'light' sebagai default
    let savedTheme = localStorage.getItem('theme') || 'light';
    applyTheme(savedTheme);

    // Event listener untuk tombol toggle
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            // Cek tema saat ini dan ganti
            if (body.classList.contains('dark-mode')) {
                savedTheme = 'light';
            } else {
                savedTheme = 'dark';
            }
            
            // Simpan tema baru ke localStorage
            localStorage.setItem('theme', savedTheme);
            // Terapkan tema baru
            applyTheme(savedTheme);
        });
    }

});