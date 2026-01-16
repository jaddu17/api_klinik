<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once "../config.php";

$data = json_decode(file_get_contents("php://input"), true);

// Validasi input
if (!$data || empty($data["username"])) {
    http_response_code(400);
    echo json_encode([
        "error" => "Username tidak boleh kosong"
    ]);
    exit;
}

$username = trim($data["username"]);

// Validasi format username
if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    http_response_code(400);
    echo json_encode([
        "error" => "Username hanya boleh berisi huruf, angka, dan underscore (_)"
    ]);
    exit;
}

// Cek apakah username sudah ada di database
$stmt = $conn->prepare("SELECT id_user FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

$exists = $stmt->num_rows > 0;

// Response
echo json_encode([
    "exists" => $exists,
    "message" => $exists ? "Username sudah digunakan" : "Username tersedia"
]);

$stmt->close();
$conn->close();
