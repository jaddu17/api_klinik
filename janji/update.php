<?php
header("Content-Type: application/json");
include '../config.php';

// Aktifkan error reporting MySQLi (debugging)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$data = json_decode(file_get_contents("php://input"));

$id = isset($data->id) ? intval($data->id) : 0;
if ($id <= 0) {
    echo json_encode(["error" => "ID tidak valid"]);
    exit;
}

$id_dokter   = isset($data->id_dokter) ? intval($data->id_dokter) : null;
$id_pasien   = isset($data->id_pasien) ? intval($data->id_pasien) : null;
$tanggal_janji = isset($data->tanggal_janji) ? $data->tanggal_janji : null;
$jam_janji     = isset($data->jam_janji) ? $data->jam_janji : null;
$keluhan       = isset($data->keluhan) ? $data->keluhan : null;
$status        = isset($data->status) ? strtolower($data->status) : null;

// Validasi data wajib
if (is_null($id_dokter) || is_null($id_pasien) || !$tanggal_janji || !$jam_janji || !$keluhan || !$status) {
    echo json_encode(["error" => "Data tidak lengkap"]);
    exit;
}

// Validasi status
$validStatus = ['menunggu', 'selesai', 'batal'];
if (!in_array($status, $validStatus)) {
    echo json_encode(["error" => "Status tidak valid"]);
    exit;
}

// Update query
$stmt = $conn->prepare("
    UPDATE janji_temu
    SET id_dokter=?, id_pasien=?, tanggal_janji=?, jam_janji=?, keluhan=?, status=?
    WHERE id_janji=?
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

try {
    $stmt->execute();

    // Ambil data terbaru langsung setelah update
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
        INNER JOIN dokter d ON j.id_dokter = d.id_dokter
        INNER JOIN pasien p ON j.id_pasien = p.id_pasien
        WHERE j.id_janji=?
    ";
    $stmt2 = $conn->prepare($sql);
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $result = $stmt2->get_result();
    $updatedJanji = $result->fetch_assoc();

    echo json_encode(["message" => "Berhasil update", "data" => $updatedJanji], JSON_UNESCAPED_UNICODE);

    $stmt2->close();
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
    exit;
}
?>
