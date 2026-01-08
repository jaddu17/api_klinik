<?php
header("Content-Type: application/json");
include '../config.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id_rekam)) {
    echo json_encode(["error" => "ID rekam medis tidak ada"]);
    exit;
}

$sql = "UPDATE rekam_medis SET
        id_janji = '$data->id_janji',
        id_tindakan = '$data->id_tindakan',
        diagnosa = '$data->diagnosa',
        catatan = '$data->catatan',
        resep = '$data->resep'
        WHERE id_rekam = '$data->id_rekam'";

if ($conn->query($sql)) {
    echo json_encode(["message" => "Rekam medis berhasil diupdate"]);
} else {
    echo json_encode(["error" => $conn->error]);
}
?>
