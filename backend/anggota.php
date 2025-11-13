<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, OPTIONS");

include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    http_response_code(200);
    exit();
}

$sql = "SELECT * FROM anggota ORDER BY id DESC";
$result = $conn->query($sql);

$anggota = [];
while ($row = $result->fetch_assoc()) {
    $anggota[] = $row;
}

echo json_encode($anggota);
?>
