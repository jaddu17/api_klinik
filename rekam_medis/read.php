<?php
header("Content-Type: application/json");
include '../config.php';

$sql = "
SELECT 
    rm.id_rekam,
    rm.id_janji,
    rm.id_tindakan,
    rm.diagnosa,
    rm.catatan,
    rm.resep,
    jt.tanggal_janji,
    t.nama_tindakan
FROM rekam_medis rm
JOIN janji_temu jt ON rm.id_janji = jt.id_janji
JOIN tindakan t ON rm.id_tindakan = t.id_tindakan
ORDER BY rm.id_rekam DESC
";

$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
