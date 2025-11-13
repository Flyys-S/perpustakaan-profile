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

// === 4. HERO BACKGROUND FADE ===
    const hero = document.querySelector('.hero');
    if (hero) {
        const backgrounds = [
            "assets/picture/2.jpg",
            "assets/picture/1.jpg",
        ];

        // buat div untuk background
        backgrounds.forEach((bg, i) => {
            const div = document.createElement('div');
            div.classList.add('hero-bg');
            div.style.backgroundImage = `url(${bg})`;
            div.style.opacity = i === 0 ? 1 : 0;
            hero.appendChild(div);
        });

        const bgDivs = document.querySelectorAll('.hero-bg');
        let current = 0;

        setInterval(() => {
            const next = (current + 1) % bgDivs.length;
            bgDivs[current].style.opacity = 0;
            bgDivs[next].style.opacity = 1;
            current = next;
        }, 5000);
    }

    // === 4B. LOGIKA MODAL BERITA (DARI BERITA.HTML) ===
    // Cek apakah elemen modal ada di halaman ini
    const modalOverlay = document.getElementById('news-modal-overlay');
    
    // Hanya jalankan kode modal jika elemen utamanya ada (agar tidak error di halaman lain)
    if (modalOverlay) {
        const modalCloseBtn = document.getElementById('modal-close-btn');
        const openModalButtons = document.querySelectorAll('.open-modal-btn');
        const modalImage = document.getElementById('modal-img');
        const modalTitle = document.getElementById('modal-title');
        const modalText = document.getElementById('modal-text');
        
        // AMBIL ELEMEN BARU UNTUK META
        const modalDate = document.getElementById('modal-date');
        const modalAuthor = document.getElementById('modal-author');

        // Fungsi untuk menutup modal
        function closeModal() {
            modalOverlay.classList.remove('active');
        }

        // Tambahkan event ke tombol "Baca Selengkapnya"
        openModalButtons.forEach(button => {
            button.addEventListener('click', (event) => {
                event.preventDefault(); // Mencegah link pindah halaman
                
                // Ambil data dari atribut 'data-*'
                const imgSrc = button.dataset.imgSrc;
                const title = button.dataset.title;
                const fullText = button.dataset.fullText;
                
                // AMBIL DATA BARU
                const date = button.dataset.date;
                const author = button.dataset.author;
                
                // Masukkan data ke modal
                modalImage.src = imgSrc;
                modalTitle.textContent = title;
                modalText.textContent = fullText;
                
                // MASUKKAN DATA BARU (Gunakan emoji 📅 dan 👤)
                if (modalDate && modalAuthor) { // Cek dulu elemennya ada
                    modalDate.textContent = date ? `📅 ${date}` : '';
                    modalAuthor.textContent = author ? `👤 ${author}` : '';
                }
                
                // Tampilkan modal
                modalOverlay.classList.add('active');
            });
        });

        // Tambahkan event ke tombol close (X)
        if (modalCloseBtn) {
            modalCloseBtn.addEventListener('click', closeModal);
        }

        // Tambahkan event ke overlay (area gelap) untuk menutup
        modalOverlay.addEventListener('click', (event) => {
            // Hanya tutup jika yang diklik adalah overlay-nya langsung
            if (event.target === modalOverlay) {
                closeModal();
            }
        });
    }
    
    // =======================================================
    // === 5. FORM SUBMISSION HANDLING (AJAX/FETCH & SQL) ===
    // =======================================================

    /**
     * Menampilkan pesan notifikasi di form
     * @param {HTMLElement} notifElement - Elemen div notifikasi
     * @param {string} message - Isi pesan
     * @param {string} type - Tipe pesan ('success' atau 'error')
     * @param {HTMLElement} formToReset - Form yang akan direset (opsional)
     */
    function showNotification(notifElement, message, type, formToReset) {
        notifElement.textContent = message;
        notifElement.className = `form-notif ${type}`;
        notifElement.style.display = "block";

        if (type === 'success' && formToReset) {
            formToReset.reset();
        }

        // Sembunyikan notifikasi setelah 5 detik
        setTimeout(() => { 
            notifElement.style.display = "none"; 
        }, 5000);
    }

    /**
     * Menangani pengiriman form melalui Fetch API (AJAX)
     * @param {HTMLElement} formElement - Elemen form
     * @param {HTMLElement} notifElement - Elemen div notifikasi
     */
    function handleFormSubmission(formElement, notifElement) {
        if (formElement) {
            formElement.addEventListener('submit', function(e) {
                e.preventDefault(); 

                const formData = new FormData(formElement);

                fetch(formElement.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    // Semua respons dari PHP harus JSON
                    return response.json().then(data => {
                        if (!response.ok) {
                            // Jika status HTTP bukan 2xx (misal 400, 409, 500), lempar error dengan pesan dari PHP
                            throw new Error(data.message || 'Terjadi error pada server.');
                        }
                        return data;
                    });
                })
                .then(data => {
                    // Respons sukses (Status 200 OK)
                    showNotification(notifElement, data.message, 'success', formElement);
                })
                .catch(err => {
                    // Tangani error jaringan atau error yang dilempar dari .then
                    showNotification(notifElement, err.message || "Gagal menghubungi server.", 'error', null);
                });
            });
        }
    }

    // --- INISIALISASI FORM BUKU (backend/api.php) ---
    const formBuku = document.getElementById('formBuku');
    if (formBuku) {
        formBuku.addEventListener('submit', function(e) {
            e.preventDefault(); // Cegah default form submission

            // Ambil semua nilai dari form
            const judul = document.getElementById('judul').value.trim();
            const anakJudul = document.getElementById('anak_judul').value.trim();
            const nomorBuku = document.getElementById('nomor_buku').value.trim();
            const pengarang = document.getElementById('pengarang').value.trim();
            const penerbit = document.getElementById('penerbit').value.trim();
            const tahunTerbit = document.getElementById('tahun_terbit').value.trim();
            const sumberBuku = document.getElementById('sumber_buku').value.trim();
            const isbn = document.getElementById('isbn').value.trim();
            const kategori = document.getElementById('kategori').value.trim();
            const bahasa = document.getElementById('bahasa').value.trim();
            const hasilDiv = document.getElementById('hasil');
            const notifDiv = document.getElementById('notif-buku');

            // Reset notifikasi
            notifDiv.style.display = 'none';
            notifDiv.textContent = '';
            notifDiv.className = 'form-notif';

            // Validasi input wajib
            if (!judul || !anakJudul || !nomorBuku || !sumberBuku) {
                notifDiv.textContent = 'Judul, Anak Judul, Nomor Buku, dan Sumber Buku wajib diisi!';
                notifDiv.className = 'form-notif error';
                notifDiv.style.display = 'block';
                return;
            }

            // Disable button saat loading
            const submitBtn = formBuku.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Menyimpan...';

            // Gunakan FormData untuk mengirim data
            const formData = new FormData();
            formData.append('judul', judul);
            formData.append('anak_judul', anakJudul);
            formData.append('nomor_buku', nomorBuku);
            formData.append('pengarang', pengarang);
            formData.append('penerbit', penerbit);
            formData.append('tahun_terbit', tahunTerbit);
            formData.append('sumber_buku', sumberBuku);
            formData.append('isbn', isbn);
            formData.append('kategori', kategori);
            formData.append('bahasa', bahasa);

            // Kirim data ke backend menggunakan fetch (local API)
            fetch('backend/api.php', {
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
                    
                    // Reset form
                    formBuku.reset();
                    
                    // Reset button
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Simpan Buku';
                } else {
                    notifDiv.className = 'form-notif error';
                    notifDiv.textContent = data.message;
                    notifDiv.style.display = 'block';
                    hasilDiv.innerHTML = '<p style="color: red;">❌ ' + data.message + '</p>';
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Simpan Buku';
                }
            })
            .catch(error => {
                notifDiv.className = 'form-notif error';
                notifDiv.textContent = 'Terjadi kesalahan: ' + error;
                notifDiv.style.display = 'block';
                hasilDiv.innerHTML = '<p style="color: red;">❌ Terjadi kesalahan: ' + error + '</p>';
                submitBtn.disabled = false;
                submitBtn.textContent = 'Simpan Buku';
            });
        });
    }

    // --- INISIALISASI FORM LOGIN ADMIN (backend/login.php) ---
    //
    // MODIFIKASI DI SINI:
    // Handle form login
        document.getElementById('loginAdmin').addEventListener('submit', function(e) {
            e.preventDefault(); // Cegah default form submission

            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            const errorDiv = document.getElementById('form-notif-error');

            // Reset error message
            errorDiv.style.display = 'none';
            errorDiv.textContent = '';

            // Validasi input
            if (!username || !password) {
                errorDiv.textContent = 'Username dan password harus diisi!';
                errorDiv.style.display = 'block';
                return;
            }

            // Disable button saat loading
            const submitBtn = document.getElementById('LoginAdmin');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Sedang Login...';

            // Kirim data ke backend menggunakan fetch
            fetch('backend/cek_login.php', {
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
                    
                    // Redirect ke admin.html setelah 1.5 detik
                    setTimeout(() => {
                        window.location.href = 'admin.html';
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
                errorDiv.textContent = 'Terjadi kesalahan: ' + error;
                errorDiv.style.display = 'block';
                submitBtn.disabled = false;
                submitBtn.textContent = 'Masuk ke Admin';
            });
        });


// 🔹 Fungsi untuk ambil data buku (untuk admin dashboard - jika diperlukan)
    // Uncomment dan modifikasi jika ingin menampilkan daftar buku di admin
    // const tbodyBuku = document.getElementById('tabel-buku-body');
    // if (tbodyBuku) {
    //     async function loadBuku() {
    //       tbodyBuku.innerHTML = "<tr><td colspan='7'>Memuat data...</td></tr>";
    //       try {
    //         const res = await fetch('backend/api.php');
    //         const data = await res.json();
    //         if (!data.length) {
    //           tbodyBuku.innerHTML = "<tr><td colspan='7'>Belum ada buku</td></tr>";
    //           return;
    //         }
    //         tbodyBuku.innerHTML = "";
    //         data.forEach(buku => {
    //           tbodyBuku.innerHTML += `<tr><td>${buku.isbn}</td><td>${buku.judul}</td><td>${buku.pengarang}</td><td>${buku.penerbit}</td><td>${buku.tahun_terbit}</td><td>${buku.stok || '-'}</td><td><a href="#">Edit</a> | <a href="#">Hapus</a></td></tr>`;
    //         });
    //       } catch (err) {
    //         tbodyBuku.innerHTML = `<tr><td colspan='7'>Gagal memuat data: ${err.message}</td></tr>`;
    //       }
    //     }
    //     loadBuku();
    // }
    }); // End of DOMContentLoaded