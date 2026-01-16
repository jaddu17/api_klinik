<?php
header("Content-Type: application/json");
require_once "../config.php";

$data = json_decode(file_get_contents("php://input"), true);

// ==========================
// VALIDASI INPUT DASAR
// ==========================
if (
    !$data ||
    empty($data["username"]) ||
    empty($data["password"]) ||
    empty($data["role"])
) {
    http_response_code(400);
    echo json_encode(["error" => "Data tidak lengkap"]);
    exit;
}

$username = trim($data["username"]);
$password = $data["password"];
$role     = $data["role"];

// ==========================
// VALIDASI FORMAT USERNAME: hanya huruf, angka, underscore
// ==========================
if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    http_response_code(400);
    echo json_encode(["error" => "Username hanya boleh berisi huruf, angka, dan underscore (_)"]);
    exit;
}

// ==========================
// VALIDASI PANJANG PASSWORD (minimal 6 karakter)
// ==========================
if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(["error" => "Password minimal 6 karakter"]);
    exit;
}

// ==========================
// CEK UNIKNES USERNAME
// ==========================
$cek = $conn->prepare("SELECT id_user FROM users WHERE username = ?");
$cek->bind_param("s", $username);
$cek->execute();
$cek->store_result();

if ($cek->num_rows > 0) {
    http_response_code(409); // Conflict
    echo json_encode(["error" => "Username sudah digunakan"]);
    exit;
}

// ==========================
// HASH PASSWORD & SIMPAN
// ==========================
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $hashedPassword, $role);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(["error" => "Gagal menyimpan user"]);
    exit;
}

// ==========================
// RESPONSE SUKSES
// ==========================
echo json_encode([
    "id_user" => (int)$stmt->insert_id,
    "username" => $username,
    "role" => $role
]);