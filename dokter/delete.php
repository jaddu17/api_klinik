<?php
header("Content-Type: application/json");
require_once '../config.php';

// Validasi input
if (!isset($_GET['id']) || empty($_GET['id'])) {
    http_response_code(400);
    echo json_encode(["error" => "ID dokter tidak valid"]);
    exit;
}

$id = (int) $_GET['id'];

// Cek apakah dokter memiliki janji temu yang belum selesai
$checkStmt = $conn->prepare(
    "SELECT COUNT(*) as count 
     FROM janji_temu 
     WHERE id_dokter = ? AND status != 'selesai'"
);
$checkStmt->bind_param("i", $id);
$checkStmt->execute();
$result = $checkStmt->get_result();
$row = $result->fetch_assoc();

if ($row['count'] > 0) {
    http_response_code(409); // Conflict
    echo json_encode([
        "error" => "Dokter tidak dapat dihapus karena masih memiliki janji temu yang belum selesai"
    ]);
    $checkStmt->close();
    $conn->close();
    exit;
}

$checkStmt->close();

// Jika tidak ada janji temu aktif, lanjutkan penghapusan
$deleteStmt = $conn->prepare("DELETE FROM dokter WHERE id_dokter = ?");
$deleteStmt->bind_param("i", $id);

if ($deleteStmt->execute()) {
    if ($deleteStmt->affected_rows > 0) {
        echo json_encode(["message" => "Dokter berhasil dihapus"]);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Dokter tidak ditemukan"]);
    }
} else {
    http_response_code(500);
    echo json_encode(["error" => "Gagal menghapus dokter: " . $conn->error]);
}

$deleteStmt->close();
$conn->close();
?>