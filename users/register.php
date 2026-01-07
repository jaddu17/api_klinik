<?php
header("Content-Type: application/json");
require_once "../config.php";

$data = json_decode(file_get_contents("php://input"), true);

if (
    !$data ||
    empty($data["username"]) ||
    empty($data["password"]) ||
    empty($data["role"])
) {
    echo json_encode([
        "success" => false,
        "message" => "Data tidak lengkap"
    ]);
    exit;
}

$username = $data["username"];
$password = password_hash($data["password"], PASSWORD_DEFAULT);
$role     = $data["role"];

/* ===============================
   CEK USERNAME
================================ */
$cek = $conn->prepare(
    "SELECT id_user FROM users WHERE username = ?"
);

if (!$cek) {
    echo json_encode([
        "success" => false,
        "message" => "Query error (cek user)"
    ]);
    exit;
}

$cek->bind_param("s", $username);
$cek->execute();
$cek->store_result();

if ($cek->num_rows > 0) {
    echo json_encode([
        "success" => false,
        "message" => "Username sudah digunakan"
    ]);
    exit;
}

/* ===============================
   INSERT USER
================================ */
$stmt = $conn->prepare(
    "INSERT INTO users (username, password, role) VALUES (?, ?, ?)"
);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Query error (insert)"
    ]);
    exit;
}

$stmt->bind_param("sss", $username, $password, $role);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Register berhasil"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Register gagal"
    ]);
}
