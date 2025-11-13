<?php
// === Header CORS & JSON ===
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json; charset=utf-8");

// === Preflight untuk CORS ===
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// === Hanya izinkan POST ===
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Gunakan metode POST"]);
    exit;
}

include "config.php";

// === Baca body request ===
$contentType = $_SERVER["CONTENT_TYPE"] ?? '';
$raw = file_get_contents("php://input");
$data = [];

// Jika JSON
if (stripos($contentType, 'application/json') !== false) {
    $data = json_decode($raw, true);
} else {
    // form-urlencoded / multipart
    $data = $_POST;
    if (empty($data) && $raw) {
        parse_str($raw, $data);
    }
}

// === Ambil username dan password ===
$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';

if ($username === '' || $password === '') {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Username dan password wajib diisi."]);
    exit;
}

// === Cek user di database ===
$q = $conn->prepare("SELECT * FROM admin WHERE username = ? LIMIT 1");
$q->bind_param("s", $username);
$q->execute();
$result = $q->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $stored = $row['password'] ?? '';

    $passwordOk = false;
    // Cek hash (ideal) atau fallback plaintext
    if ($stored !== '' && password_verify($password, $stored)) {
        $passwordOk = true;
    } elseif ($password === $stored) {
        $passwordOk = true;
    }

    if ($passwordOk) {
        $token = bin2hex(random_bytes(16));
        $up = $conn->prepare("UPDATE admin SET token = ? WHERE username = ?");
        $up->bind_param("ss", $token, $username);
        $up->execute();

        echo json_encode([
            "success" => true,
            "message" => "Login berhasil",
            "token" => $token
        ]);
        exit;
    } else {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Password salah."]);
        exit;
    }
} else {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Username tidak ditemukan."]);
    exit;
}
?>
