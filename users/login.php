<?php
header("Content-Type: application/json");
require_once "../config.php";

$data = json_decode(file_get_contents("php://input"), true);

// Validasi input
if (!$data || empty($data["username"]) || empty($data["password"])) {
    echo json_encode([
        "success" => false,
        "message" => "Data tidak lengkap"
    ]);
    exit;
}

$username = $data["username"];
$password = $data["password"];

// Prepared Statement (AMAN)
$stmt = $conn->prepare("SELECT id_user, username, password, role FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo json_encode([
        "success" => false,
        "message" => "Username tidak ditemukan"
    ]);
    exit;
}

// Verifikasi password
if (!password_verify($password, $user["password"])) {
    echo json_encode([
        "success" => false,
        "message" => "Password salah"
    ]);
    exit;
}

// Response sukses
echo json_encode([
    "success" => true,
    "message" => "Login berhasil",
    "id_user" => $user["id_user"],
    "role" => $user["role"]
]);
