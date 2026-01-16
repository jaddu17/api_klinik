<?php
header("Content-Type: application/json");
include '../config.php';

$data = json_decode(file_get_contents("php://input"));

// ==========================
// VALIDASI ID
// ==========================
$id = isset($data->id) ? (int) $data->id : 0;
if ($id <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "ID janji tidak valid"
    ]);
    exit;
}

// ==========================
// AMBIL DATA
// ==========================
$id_dokter = isset($data->id_dokter) ? (int) $data->id_dokter : null;
$id_pasien = isset($data->id_pasien) ? (int) $data->id_pasien : null;
$tanggal_janji = $data->tanggal_janji ?? null;
$jam_janji = $data->jam_janji ?? null;
$keluhan = $data->keluhan ?? null;
$status = isset($data->status) ? strtolower($data->status) : null;

// ==========================
// VALIDASI INPUT
// ==========================
if (
    !$id_dokter || !$id_pasien ||
    !$tanggal_janji || !$jam_janji ||
    !$keluhan || !$status
) {
    echo json_encode([
        "success" => false,
        "message" => "Data tidak lengkap"
    ]);
    exit;
}

// ==========================
// VALIDASI STATUS
// ==========================
$validStatus = ['konfirmasi', 'selesai', 'dibatalkan', 'tidak_hadir'];
if (!in_array($status, $validStatus)) {
    echo json_encode([
        "success" => false,
        "message" => "Status tidak valid"
    ]);
    exit;
}

// ==========================
// CEK JANJI DOUBLE (KECUALI DIRI SENDIRI)
// ==========================
$cek = $conn->prepare("
    SELECT id_janji
    FROM janji_temu
    WHERE id_dokter = ?
      AND tanggal_janji = ?
      AND jam_janji = ?
      AND id_janji != ?
    LIMIT 1
");

$cek->bind_param("issi", $id_dokter, $tanggal_janji, $jam_janji, $id);
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
// UPDATE DATA
// ==========================
$stmt = $conn->prepare("
    UPDATE janji_temu
    SET
        id_dokter = ?,
        id_pasien = ?,
        tanggal_janji = ?,
        jam_janji = ?,
        keluhan = ?,
        status = ?
    WHERE id_janji = ?
");

$stmt->bind_param(
    "iissssi",
    $id_dokter,
    $id_pasien,
    $tanggal_janji,
    $jam_janji,
    $keluhan,
    $status,
    $id
);

if (!$stmt->execute()) {
    echo json_encode([
        "success" => false,
        "message" => "Gagal update janji temu"
    ]);
    exit;
}
$stmt->close();

// ==========================
// AMBIL DATA TERBARU
// ==========================
$sql = "
    SELECT 
        j.id_janji AS id,
        j.id_dokter,
        j.id_pasien,
        j.tanggal_janji,
        j.jam_janji,
        j.keluhan,
        j.status,
        d.nama_dokter,
        p.nama_pasien
    FROM janji_temu j
    JOIN dokter d ON j.id_dokter = d.id_dokter
    JOIN pasien p ON j.id_pasien = p.id_pasien
    WHERE j.id_janji = ?
";

$stmt2 = $conn->prepare($sql);
$stmt2->bind_param("i", $id);
$stmt2->execute();
$result = $stmt2->get_result();
$dataUpdated = $result->fetch_assoc();

echo json_encode([
    "success" => true,
    "message" => "Janji temu berhasil diperbarui",
    "data" => $dataUpdated
], JSON_UNESCAPED_UNICODE);

$stmt2->close();
$conn->close();
