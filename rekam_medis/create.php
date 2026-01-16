<?php
header("Content-Type: application/json");
include '../config.php';

$data = json_decode(file_get_contents("php://input"), true);

$id_janji     = $data['id_janji'] ?? null;
$id_tindakan  = $data['id_tindakan'] ?? null;
$diagnosa     = $data['diagnosa'] ?? '';
$catatan      = $data['catatan'] ?? '';
$resep        = $data['resep'] ?? '';

if (!$id_janji || !$id_tindakan) {
    echo json_encode(["error" => "Data tidak lengkap"]);
    exit;
}

/**
 * 🔥 Ambil id_pasien dari janji_temu
 */
$q = $conn->prepare(
    "SELECT id_pasien FROM janji_temu WHERE id_janji = ?"
);
$q->bind_param("i", $id_janji);
$q->execute();
$result = $q->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo json_encode(["error" => "Janji temu tidak ditemukan"]);
    exit;
}

$id_pasien = $row['id_pasien'];

/**
 * 🔒 Cegah 1 janji punya >1 rekam medis
 */
$cek = $conn->prepare(
    "SELECT id_rekam FROM rekam_medis WHERE id_janji = ?"
);
$cek->bind_param("i", $id_janji);
$cek->execute();
$cekRes = $cek->get_result();

if ($cekRes->num_rows > 0) {
    echo json_encode(["error" => "Rekam medis untuk janji ini sudah ada"]);
    exit;
}

/**
 * ✅ Insert rekam medis
 */
$stmt = $conn->prepare("
    INSERT INTO rekam_medis
    (id_janji, id_pasien, id_tindakan, diagnosa, catatan, resep)
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "iiisss",
    $id_janji,
    $id_pasien,
    $id_tindakan,
    $diagnosa,
    $catatan,
    $resep
);

if ($stmt->execute()) {

    // Optional: update status janji
    $conn->query(
        "UPDATE janji_temu SET status = 'selesai' WHERE id_janji = $id_janji"
    );

    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => $stmt->error]);
}

$conn->close();
