<?php
header("Content-Type: application/json");
include '../config.php';

/**
 * Ambil ID dari:
 * - query string
 * - body DELETE
 */
parse_str(file_get_contents("php://input"), $data);

$id = $_GET['id']
    ?? $data['id']
    ?? null;

if (!$id) {
    echo json_encode(["error" => "ID rekam medis tidak ada"]);
    exit;
}

/**
 * Cek apakah data ada
 */
$cek = $conn->prepare(
    "SELECT id_rekam FROM rekam_medis WHERE id_rekam = ?"
);
$cek->bind_param("i", $id);
$cek->execute();
$res = $cek->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["error" => "Data rekam medis tidak ditemukan"]);
    exit;
}

/**
 * Hapus rekam medis
 */
$stmt = $conn->prepare(
    "DELETE FROM rekam_medis WHERE id_rekam = ?"
);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Rekam medis berhasil dihapus"
    ]);
} else {
    echo json_encode(["error" => $stmt->error]);
}

$conn->close();
