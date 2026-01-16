<?php
header("Content-Type: application/json");
include '../config.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    echo json_encode(["success" => false, "message" => "ID tidak valid"]);
    exit;
}

// Cek apakah ada rekam medis untuk janji ini
$cek = $conn->prepare("SELECT id_rekam FROM rekam_medis WHERE id_janji = ? LIMIT 1");
$cek->bind_param("i", $id);
$cek->execute();
$cek->store_result();

if ($cek->num_rows > 0) {
    echo json_encode([
        "success" => false,
        "message" => "Tidak dapat menghapus janji temu yang sudah memiliki rekam medis"
    ]);
    exit;
}
$cek->close();

$stmt = $conn->prepare("DELETE FROM janji_temu WHERE id_janji = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Janji temu berhasil dihapus"]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal menghapus janji temu: " . $conn->error]);
}

$stmt->close();
$conn->close();
?>