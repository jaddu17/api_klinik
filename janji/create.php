<?php
header("Content-Type: application/json");
include '../config.php';

$data = json_decode(file_get_contents("php://input"));

if (
    empty($data->id_dokter) ||
    empty($data->id_pasien) ||
    empty($data->tanggal_janji) ||
    empty($data->jam_janji) ||
    empty($data->keluhan)
) {
    echo json_encode(["success" => false, "message" => "Input tidak lengkap"]);
    exit;
}

$status = isset($data->status) ? strtolower($data->status) : "menunggu";

$stmt = $conn->prepare("
    INSERT INTO janji_temu (id_dokter, id_pasien, tanggal_janji, jam_janji, keluhan, status)
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "iissss",
    $data->id_dokter,
    $data->id_pasien,
    $data->tanggal_janji,
    $data->jam_janji,
    $data->keluhan,
    $status
);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Berhasil tambah data"]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
