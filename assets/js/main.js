document.addEventListener("DOMContentLoaded", function() {

    // === 1. HAMBURGER MENU ===
    const hamburgerMenu = document.getElementById('hamburger-menu');
    const navLinks = document.querySelector('.nav-links');

    if (hamburgerMenu) {
        hamburgerMenu.addEventListener('click', function() {
            navLinks.classList.toggle('active');
            hamburgerMenu.classList.toggle('active');
        });
    }

    // === 2. LIGHT/DARK MODE ===
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

    // === 3. TYPEWRITER HERO ===
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

    // === 4. HERO BACKGROUND FADE (DIPERBAIKI) ===
    const hero = document.querySelector('.hero');
    if (hero) {
        // KEMBALIKAN KE GAMBAR ASLI (2.jpg dan 1.jpg)
        // Pastikan kedua file ini ada di folder assets/picture
        const backgrounds = [
            "../assets/picture/2.jpg", 
            "../assets/picture/1.jpg"
        ];

        // Bersihkan background lama agar tidak menumpuk saat refresh (Hapus duplikat)
        const existingBgs = hero.querySelectorAll('.hero-bg');
        existingBgs.forEach(bg => bg.remove());

        // Buat elemen background baru
        backgrounds.forEach((bg, i) => {
            const div = document.createElement('div');
            div.classList.add('hero-bg');
            div.style.backgroundImage = `url('${bg}')`; 
            // Gambar pertama opacity 1, sisanya 0
            div.style.opacity = i === 0 ? 1 : 0;
            hero.appendChild(div);
        });

        const bgDivs = document.querySelectorAll('.hero-bg');
        let current = 0;

        // Jalankan interval ganti gambar setiap 5 detik
        if (bgDivs.length > 1) { // Hanya jalankan jika ada lebih dari 1 gambar
            setInterval(() => {
                const next = (current + 1) % bgDivs.length;
                
                // Efek fade: sembunyikan yang sekarang, tampilkan yang berikutnya
                bgDivs[current].style.opacity = 0;
                bgDivs[next].style.opacity = 1;
                
                current = next;
            }, 5000);
        }
    }

    // === 5. MODAL BERITA ===
    const modalOverlay = document.getElementById('news-modal-overlay');
    if (modalOverlay) {
        const modalCloseBtn = document.getElementById('modal-close-btn');
        const openModalButtons = document.querySelectorAll('.open-modal-btn');
        const modalImage = document.getElementById('modal-img');
        const modalTitle = document.getElementById('modal-title');
        const modalText = document.getElementById('modal-text');
        const modalDate = document.getElementById('modal-date');
        const modalAuthor = document.getElementById('modal-author');

        function closeModal() {
            modalOverlay.classList.remove('active');
        }

        openModalButtons.forEach(button => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                const imgSrc = button.dataset.imgSrc;
                const title = button.dataset.title;
                const fullText = button.dataset.fullText;
                const date = button.dataset.date;
                const author = button.dataset.author;
                
                modalImage.src = imgSrc;
                modalTitle.textContent = title;
                modalText.textContent = fullText;
                
                if (modalDate && modalAuthor) {
                    modalDate.textContent = date ? `📅 ${date}` : '';
                    modalAuthor.textContent = author ? `👤 ${author}` : '';
                }
                modalOverlay.classList.add('active');
            });
        });

        if (modalCloseBtn) {
            modalCloseBtn.addEventListener('click', closeModal);
        }

        modalOverlay.addEventListener('click', (event) => {
            if (event.target === modalOverlay) {
                closeModal();
            }
        });
    }
    
    // =======================================================
    // === 6. FORM HANDLING (BACKEND PATH SUDAH BENAR) ===
    // =======================================================

    // --- Handling Form Buku ---
    const formBuku = document.getElementById('formBuku');
    if (formBuku) {
        formBuku.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(formBuku);
            const hasilDiv = document.getElementById('hasil');
            const notifDiv = document.getElementById('notif-buku');
            const submitBtn = formBuku.querySelector('button[type="submit"]');

            // Reset notifikasi
            notifDiv.style.display = 'none';
            notifDiv.textContent = '';
            
            // Disable tombol
            submitBtn.disabled = true;
            submitBtn.textContent = 'Menyimpan...';

            // Path backend mundur satu folder
            fetch('../backend/api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    notifDiv.className = 'form-notif success';
                    notifDiv.textContent = data.message;
                    notifDiv.style.display = 'block';
                    hasilDiv.innerHTML = '<p style="color: green;">✅ ' + data.message + '</p>';
                    formBuku.reset();
                } else {
                    notifDiv.className = 'form-notif error';
                    notifDiv.textContent = data.message;
                    notifDiv.style.display = 'block';
                    hasilDiv.innerHTML = '<p style="color: red;">❌ ' + data.message + '</p>';
                }
                submitBtn.disabled = false;
                submitBtn.textContent = 'Simpan Buku';
            })
            .catch(error => {
                notifDiv.className = 'form-notif error';
                notifDiv.textContent = 'Terjadi kesalahan: ' + error;
                notifDiv.style.display = 'block';
                hasilDiv.innerHTML = '<p style="color: red;">❌ Koneksi Gagal</p>';
                submitBtn.disabled = false;
                submitBtn.textContent = 'Simpan Buku';
            });
        });
    }

    // --- Handling Form Login Admin ---
    const loginForm = document.getElementById('loginAdmin');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            const errorDiv = document.getElementById('form-notif-error');
            const submitBtn = document.getElementById('LoginAdmin');

            errorDiv.style.display = 'none';

            if (!username || !password) {
                errorDiv.textContent = 'Username dan password harus diisi!';
                errorDiv.style.display = 'block';
                return;
            }

            submitBtn.disabled = true;
            submitBtn.textContent = 'Sedang Login...';

            // Path backend mundur satu folder
            fetch('../backend/cek_login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'username=' + encodeURIComponent(username) + '&password=' + encodeURIComponent(password)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    errorDiv.className = 'form-notif success';
                    errorDiv.textContent = data.message;
                    errorDiv.style.display = 'block';
                    
                    // Redirect tetap admin.php karena satu folder
                    setTimeout(() => {
                        window.location.href = 'admin.php';
                    }, 1500);
                } else {
                    errorDiv.className = 'form-notif error';
                    errorDiv.textContent = data.message;
                    errorDiv.style.display = 'block';
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Masuk ke Admin';
                }
            })
            .catch(error => {
                errorDiv.className = 'form-notif error';
                errorDiv.textContent = 'Terjadi kesalahan koneksi';
                errorDiv.style.display = 'block';
                submitBtn.disabled = false;
                submitBtn.textContent = 'Masuk ke Admin';
            });
        });
    }
});