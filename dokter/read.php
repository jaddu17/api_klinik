<?php
include '../config.php';

$sql = "SELECT * FROM dokter";
$result = $conn->query($sql);

$output = [];
while ($row = $result->fetch_assoc()) {
    $output[] = [
        "id_dokter"     => (int)$row["id_dokter"],
        "nama_dokter"   => $row["nama_dokter"],
        "spesialisasi"  => $row["spesialisasi"],
        "nomor_telepon" => $row["nomor_telepon"]
    ];
}

echo json_encode($output, JSON_UNESCAPED_UNICODE);
?>
