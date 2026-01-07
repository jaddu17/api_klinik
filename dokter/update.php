<?php
include '../config.php';

$data = json_decode(file_get_contents("php://input"));

$id = $data->id_dokter;

$sql = "UPDATE dokter SET 
        nama_dokter='$data->nama_dokter',
        spesialisasi='$data->spesialisasi',
        nomor_telepon='$data->nomor_telepon'
        WHERE id_dokter=$id";

if ($conn->query($sql)) {
    echo json_encode(["message" => "Data dokter diperbarui"]);
} else {
    echo json_encode(["error" => $conn->error]);
}
?>
