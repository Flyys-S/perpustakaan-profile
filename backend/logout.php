<?php
session_start();

// Default tujuan: Kembali ke Login Admin
$tujuan_redirect = '../pages/login.html'; 

// Jika yang login adalah Anggota, arahkan ke Login Anggota
if (isset($_SESSION['login_anggota']) && $_SESSION['login_anggota'] === true) {
    $tujuan_redirect = '../pages/login_anggota.html';
}

// Hapus semua data sesi
session_unset();  
session_destroy(); 

// Redirect ke halaman yang sesuai
header("Location: " . $tujuan_redirect);
exit;
?>