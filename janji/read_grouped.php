<?php
header("Content-Type: application/json");
require_once "../config.php";

$sql = "
SELECT
    d.id_dokter,
    d.nama_dokter,

    j.id_janji AS id,
    j.id_pasien,
    j.tanggal_janji,
    j.jam_janji,
    j.keluhan,
    j.status,

    p.nama_pasien,
    EXISTS (
        SELECT 1 
        FROM rekam_medis rm 
        WHERE rm.id_janji = j.id_janji
    ) AS sudah_ada_rekam_medis
FROM dokter d
LEFT JOIN janji_temu j ON d.id_dokter = j.id_dokter
LEFT JOIN pasien p ON j.id_pasien = p.id_pasien
ORDER BY d.nama_dokter, j.tanggal_janji, j.jam_janji
";

$result = $conn->query($sql);
$data = [];

while ($row = $result->fetch_assoc()) {

    $idDokter = $row['id_dokter'];

    if (!isset($data[$idDokter])) {
        $data[$idDokter] = [
            "id_dokter" => (int) $row['id_dokter'],
            "nama_dokter" => $row['nama_dokter'],
            "janji_temu" => []
        ];
    }

    if (!is_null($row['id'])) {
        $data[$idDokter]['janji_temu'][] = [
            "id" => (int) $row['id'],
            "id_dokter" => (int) $row['id_dokter'],
            "id_pasien" => (int) $row['id_pasien'],
            "tanggal_janji" => $row['tanggal_janji'],
            "jam_janji" => $row['jam_janji'],
            "keluhan" => $row['keluhan'],
            "status" => $row['status'],
            "nama_pasien" => $row['nama_pasien'],
            "sudah_ada_rekam_medis" => (bool) $row['sudah_ada_rekam_medis']
        ];
    }
}

echo json_encode(array_values($data));
