<?php
include '../config.php';

$data = json_decode(file_get_contents("php://input"));

$nama = $data->nama_dokter;
$spesialisasi = $data->spesialisasi;
$telp = $data->nomor_telepon;
$sql = "INSERT INTO dokter (nama_dokter, spesialisasi, nomor_telepon)
VALUES ('$nama', '$spesialisasi', '$telp')";

if ($conn->query($sql)) {
    echo json_encode(["message" => "Dokter berhasil ditambahkan"]);
} else {
    echo json_encode(["error" => $conn->error]);
}
?>
