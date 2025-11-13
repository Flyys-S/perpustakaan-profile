<?php
$host = "localhost";
$user = "root"; // default Laragon
$pass = "";     // kosong kalau belum kamu ubah
$db   = "perpustakaan_db"; // nanti kita buat di phpMyAdmin

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => $conn->connect_error]));
}

// Header untuk mengizinkan akses dari frontend
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle preflight (OPTIONS) request agar fetch() tidak error
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
?>
