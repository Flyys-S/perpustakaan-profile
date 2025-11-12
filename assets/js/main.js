// Menunggu sampai semua konten HTML dimuat
document.addEventListener("DOMContentLoaded", function() {

    // === 1. FUNGSI MOBILE MENU (HAMBURGER) ===
    const hamburgerMenu = document.getElementById('hamburger-menu');
    const navLinks = document.querySelector('.nav-links');

    if (hamburgerMenu) {
        hamburgerMenu.addEventListener('click', function() {
            navLinks.classList.toggle('active');
            hamburgerMenu.classList.toggle('active');
        });
    }

    // === 2. FUNGSI LIGHT/DARK MODE ===
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;

    function applyTheme(theme) {
        if (theme === 'dark') {
            body.classList.add('dark-mode');
            themeToggle.textContent = '☀️';
        } else {
            body.classList.remove('dark-mode');
            themeToggle.textContent = '🌙';
        }
    }

    let savedTheme = localStorage.getItem('theme') || 'light';
    applyTheme(savedTheme);

    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            savedTheme = body.classList.contains('dark-mode') ? 'light' : 'dark';
            localStorage.setItem('theme', savedTheme);
            applyTheme(savedTheme);
        });
    }

    // === 3. ANIMASI KETIKAN (TYPEWRITER) HERO ===
    const textElement = document.querySelector('.hero-content p');
    if (textElement) {
        const messages = [
            "Jendela Anda menuju dunia pengetahuan.",
            "Temukan ilmu baru setiap hari.",
            "Nikmati pengalaman membaca tanpa batas.",
            "Jelajahi koleksi kami dan temukan petualangan baru."
        ];

        let messageIndex = 0;
        let charIndex = 0;
        let isDeleting = false;
        const typingSpeed = 100;
        const eraseSpeed = 60;
        const delayBetween = 1800;

        function type() {
            const currentMessage = messages[messageIndex];
            if (isDeleting) {
                textElement.textContent = currentMessage.substring(0, charIndex--);
                if (charIndex < 0) {
                    isDeleting = false;
                    messageIndex = (messageIndex + 1) % messages.length;
                    setTimeout(type, 500);
                } else {
                    setTimeout(type, eraseSpeed);
                }
            } else {
                textElement.textContent = currentMessage.substring(0, charIndex++);
                if (charIndex === currentMessage.length) {
                    isDeleting = true;
                    setTimeout(type, delayBetween);
                } else {
                    setTimeout(type, typingSpeed);
                }
            }
        }

        type();
    }

});
