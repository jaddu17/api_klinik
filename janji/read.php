<?php
header("Content-Type: application/json; charset=UTF-8");
include '../config.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "
SELECT 
    j.id_janji,
    j.id_dokter,
    j.id_pasien,
    j.tanggal_janji,
    j.jam_janji,
    j.keluhan,
    j.status,
    d.nama_dokter,
    p.nama_pasien,
    EXISTS (
        SELECT 1 
        FROM rekam_medis rm 
        WHERE rm.id_janji = j.id_janji
    ) AS sudah_ada_rekam_medis
FROM janji_temu j
INNER JOIN dokter d ON j.id_dokter = d.id_dokter
INNER JOIN pasien p ON j.id_pasien = p.id_pasien
";

if ($search !== '') {
    $search = $conn->real_escape_string($search);
    $sql .= "
    WHERE 
        p.nama_pasien LIKE '%$search%' OR
        d.nama_dokter LIKE '%$search%' OR
        j.tanggal_janji LIKE '%$search%'
    ";
}

$sql .= " ORDER BY j.tanggal_janji DESC, j.jam_janji DESC";

$result = $conn->query($sql);

$output = [];
while ($row = $result->fetch_assoc()) {
    $output[] = [
        "id" => (int) $row["id_janji"],
        "id_dokter" => (int) $row["id_dokter"],
        "id_pasien" => (int) $row["id_pasien"],
        "tanggal_janji" => $row["tanggal_janji"],
        "jam_janji" => $row["jam_janji"],
        "keluhan" => $row["keluhan"],
        "status" => !empty($row["status"]) ? $row["status"] : "konfirmasi",
        "nama_dokter" => $row["nama_dokter"],
        "nama_pasien" => $row["nama_pasien"],
        "sudah_ada_rekam_medis" => (bool) $row["sudah_ada_rekam_medis"]
    ];
}

echo json_encode($output, JSON_UNESCAPED_UNICODE);
$conn->close();
