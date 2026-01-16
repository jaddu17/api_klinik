<?php
header("Content-Type: application/json");
include '../config.php';

$data = json_decode(file_get_contents("php://input"));

// ==========================
// VALIDASI INPUT
// ==========================
if (
    empty($data->id_dokter) ||
    empty($data->id_pasien) ||
    empty($data->tanggal_janji) ||
    empty($data->jam_janji)
) {
    echo json_encode([
        "success" => false,
        "message" => "Input tidak lengkap"
    ]);
    exit;
}

$id_dokter = (int) $data->id_dokter;
$id_pasien = (int) $data->id_pasien;
$tanggal = $data->tanggal_janji;
$jam = $data->jam_janji;
$keluhan = isset($data->keluhan) ? $data->keluhan : ""; // Keluhan bisa kosong
$status = isset($data->status) ? strtolower($data->status) : "konfirmasi";


// ==========================
// CEK JANJI DOUBLE
// ==========================
$cek = $conn->prepare("
    SELECT id_janji
    FROM janji_temu
    WHERE id_dokter = ?
      AND tanggal_janji = ?
      AND jam_janji = ?
    LIMIT 1
");

$cek->bind_param("iss", $id_dokter, $tanggal, $jam);
$cek->execute();
$cek->store_result();

if ($cek->num_rows > 0) {
    echo json_encode([
        "success" => false,
        "message" => "Dokter sudah memiliki janji pada waktu tersebut"
    ]);
    exit;
}
$cek->close();


// ==========================
// INSERT DATA
// ==========================
$stmt = $conn->prepare("
    INSERT INTO janji_temu
    (id_dokter, id_pasien, tanggal_janji, jam_janji, keluhan, status)
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "iissss",
    $id_dokter,
    $id_pasien,
    $tanggal,
    $jam,
    $keluhan,
    $status
);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Janji temu berhasil dibuat"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Gagal menyimpan janji temu"
    ]);
}

$stmt->close();
$conn->close();
