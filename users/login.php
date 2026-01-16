<?php
header("Content-Type: application/json");
require_once "../config.php";

$data = json_decode(file_get_contents("php://input"), true);

// Validasi input
if (!$data || empty($data["username"]) || empty($data["password"])) {
    http_response_code(400);
    echo json_encode([
        "error" => "Data tidak lengkap"
    ]);
    exit;
}

$username = $data["username"];
$password = $data["password"];

// Prepared Statement (AMAN)
$stmt = $conn->prepare(
    "SELECT id_user, username, password, role FROM users WHERE username = ?"
);
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Username tidak ditemukan
if (!$user) {
    http_response_code(401);
    echo json_encode([
        "error" => "Username tidak ditemukan"
    ]);
    exit;
}

// Password salah
if (!password_verify($password, $user["password"])) {
    http_response_code(401);
    echo json_encode([
        "error" => "Password salah"
    ]);
    exit;
}

// ==========================
// RESPONSE SUKSES (USER ONLY)
// ==========================
echo json_encode([
    "id_user" => (int) $user["id_user"],
    "username" => $user["username"],
    "role" => $user["role"]
]);
