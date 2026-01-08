<?php
header("Content-Type: application/json");
include '../config.php';

// Ambil data JSON dari request body
$data = json_decode(file_get_contents("php://input"));

// Validasi sederhana
if (
    !isset($data->id_janji) ||
    !isset($data->id_tindakan) ||
    !isset($data->diagnosa)
) {
    echo json_encode(["error" => "Data tidak lengkap"]);
    exit;
}

// Query INSERT (id_rekam TIDAK disertakan karena AUTO INCREMENT)
$sql = "INSERT INTO rekam_medis 
        (id_janji, id_tindakan, diagnosa, catatan, resep) 
        VALUES (
            '$data->id_janji',
            '$data->id_tindakan',
            '$data->diagnosa',
            '$data->catatan',
            '$data->resep'
        )";

if ($conn->query($sql)) {
    echo json_encode([
        "message" => "Rekam medis berhasil ditambahkan"
    ]);
} else {
    echo json_encode([
        "error" => $conn->error
    ]);
}
?>
