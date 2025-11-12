<?php
// koneksi.php
$host = 'localhost';
// ...
try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // Pastikan tidak ada ECHO atau print_r di sini.
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
     exit; // Penting: harus ada exit setelah throw jika fatal
}
?> // Pastikan tidak ada baris kosong di bawah ini