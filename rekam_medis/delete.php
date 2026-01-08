<?php
header("Content-Type: application/json");
include '../config.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id_rekam)) {
    echo json_encode(["error" => "ID rekam medis tidak ada"]);
    exit;
}

$sql = "DELETE FROM rekam_medis WHERE id_rekam = '$data->id_rekam'";

if ($conn->query($sql)) {
    echo json_encode(["message" => "Rekam medis berhasil dihapus"]);
} else {
    echo json_encode(["error" => $conn->error]);
}
?>
