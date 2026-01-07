<?php
header("Content-Type: application/json");
include '../config.php';

$sql = "SELECT 
            j.id_janji,
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
        INNER JOIN pasien p ON j.id_pasien = p.id_pasien";


$result = $conn->query($sql);

$output = [];
while ($row = $result->fetch_assoc()) {
    $output[] = [
        "id"            => (int)$row["id_janji"],
        "id_dokter"     => (int)$row["id_dokter"],
        "id_pasien"     => (int)$row["id_pasien"],
        "tanggal_janji" => $row["tanggal_janji"],
        "jam_janji"     => $row["jam_janji"],
        "keluhan"       => $row["keluhan"],
        "status"        => $row["status"],     // lowercase → cocok dengan enum Kotlin
        "nama_dokter"   => $row["nama_dokter"],
        "nama_pasien"   => $row["nama_pasien"]
    ];
}

echo json_encode($output, JSON_UNESCAPED_UNICODE);

$conn->close();
?>
