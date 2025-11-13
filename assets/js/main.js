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

    // --- INISIALISASI FORM BUKU (backend/tambah_buku.php) ---
    const form = document.querySelector(".data-form");
    const hasil = document.getElementById("hasil");

// Ganti URL ini dengan URL ngrok kamu
    const API_URL = "https://unnautical-eladia-nonsaleably.ngrok-free.dev/perpustakaan-backend/api.php";

form.addEventListener("submit", async (e) => {
  e.preventDefault();

  const data = {
    judul: document.getElementById("judul").value,
    anak_judul: document.getElementById("anak_judul").value,
    pengarang: document.getElementById("pengarang").value,
    penerbit: document.getElementById("penerbit").value,
    tahun_terbit: document.getElementById("tahun_terbit").value,
    sumber_buku: document.getElementById("sumber_buku").value,
    isbn: document.getElementById("isbn").value,
    kategori: document.getElementById("kategori").value,
    bahasa: document.getElementById("bahasa").value
  };

  try {
    const res = await fetch(API_URL, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data)
    });

    const result = await res.json();
    hasil.textContent = result.message || result.error;
    hasil.style.color = result.message ? "green" : "red";

    form.reset();
    loadBuku(); // otomatis tampilkan ulang daftar buku
  } catch (err) {
    hasil.textContent = "Gagal mengirim data: " + err.message;
    hasil.style.color = "red";
  }
});

    // --- INISIALISASI FORM LOGIN ADMIN (backend/login.php) ---
    //
    // MODIFIKASI DI SINI:
    // token admin
    const isProtectedPage = !!document.getElementById('logout') || document.body.classList.contains('admin-page');

if (isProtectedPage) {
    const token = localStorage.getItem('token');
    console.log('[AUTH] isProtectedPage=true, token=', token);

    if (!token) {
        console.log('[AUTH] token tidak ditemukan -> redirect ke login');
        if (!location.pathname.endsWith('login.html')) location.href = 'login.html';
    } else {
        // debug: panggil check_token dan log detailnya
        const CHECK_URL = 'https://unnautical-eladia-nonsaleably.ngrok-free.dev/perpustakaan-backend/check_token.php?token=' + encodeURIComponent(token);
        console.log('[AUTH] cek token ke', CHECK_URL);

        fetch(CHECK_URL, { method: 'GET' })
            .then(async res => {
                const text = await res.text();
                console.log('[AUTH] check_token status=', res.status, 'text=', text);
                let data = null;
                try { data = JSON.parse(text); } catch (err) { console.warn('[AUTH] check_token bukan JSON:', err); }

                // bila format tidak sesuai, jangan langsung redirect — tampilkan alert dan keluarkan token utk debugging
                if (!res.ok || !data) {
                    console.warn('[AUTH] check_token error atau respons invalid, tidak otomatis redirect. Hapus token jika memang sengaja invalid.');
                    // uncomment jika ingin otomatis logout ketika invalid:
                    // localStorage.removeItem('token'); location.href = 'login.html';
                    return;
                }

                if (!data.valid) {
                    console.log('[AUTH] token tidak valid menurut server -> hapus token dan redirect ke login');
                    localStorage.removeItem('token');
                    location.href = 'login.html';
                } else {
                    console.log('[AUTH] token valid, user tetap di halaman');
                }
            })
            .catch(err => {
                console.error('[AUTH] fetch check_token gagal:', err);
                // jangan langsung redirect supaya bisa debugging; hapus token jika yakin server down
                // localStorage.removeItem('token'); location.href = 'login.html';
            });
    }
}
    // Dua blok kode loginForm yang konflik telah dihapus
    // dan diganti dengan satu blok yang benar di bawah ini.
    //
    document.getElementById('loginAdmin')?.addEventListener('submit', async (e) => {
  e.preventDefault();

  const username = document.getElementById('username').value.trim();
  const password = document.getElementById('password').value.trim();

  const LOGIN_URL = 'https://unnautical-eladia-nonsaleably.ngrok-free.dev/perpustakaan-backend/masuk.php';
  console.log('🔹 KIRIM LOGIN ->', LOGIN_URL);

  try {
    const res = await fetch(LOGIN_URL, {
      method: 'POST',
      body: new URLSearchParams({ username, password })
      // ⚠️ JANGAN tambahkan headers Content-Type manual
      // biarkan browser otomatis kirim form-urlencoded
    });

    const text = await res.text();
    console.log('🔹 RESPON RAW:', text);

    let data;
    try { 
      data = JSON.parse(text); 
    } catch (err) { 
      console.error('❌ JSON parse error:', err);
      throw new Error('Respon bukan JSON');
    }

    if (data.success) {
      localStorage.setItem('token', data.token);
      alert('✅ Login berhasil');
      location.href = 'admin.html';
    } else {
      alert('❌ ' + (data.message || 'Login gagal'));
    }

  } catch (err) {
    console.error('❌ ERROR:', err);
    alert('Gagal menghubungi server: ' + err.message);
  }
});


// 🔹 Fungsi untuk ambil data buku
async function loadBuku() {
  tbody.innerHTML = "<tr><td colspan='9'>Memuat data...</td></tr>";
  try {
    const res = await fetch(API_URL);
    const data = await res.json();

    if (!data.length) {
      tbody.innerHTML = "<tr><td colspan='9'>Belum ada buku</td></tr>";
      return;
    }

    tbody.innerHTML = "";
    data.forEach(buku => {
      tbody.innerHTML += `
        <tr>
          <td>${buku.judul}</td>
          <td>${buku.anak_judul}</td>
          <td>${buku.pengarang}</td>
          <td>${buku.penerbit}</td>
          <td>${buku.tahun_terbit}</td>
          <td>${buku.sumber_buku}</td>
          <td>${buku.isbn}</td>
          <td>${buku.kategori}</td>
          <td>${buku.bahasa}</td>
        </tr>`;
    });
  } catch (err) {
    tbody.innerHTML = `<tr><td colspan='9'>Gagal memuat data: ${err.message}</td></tr>`;
  }
}

loadBuku();
    // --- INISIALISASI FORM ANGGOTA (backend/daftar_anggota.php) ---
    const formAnggota = document.querySelector('form[action="backend/daftar_anggota.php"]');
    const notifAnggota = document.getElementById('notif-anggota');
    handleFormSubmission(formAnggota, notifAnggota);
    }); // End of DOMContentLoaded