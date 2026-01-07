<?php
include '../config.php';

$data = json_decode(file_get_contents("php://input"));

$sql = "INSERT INTO pasien 
(nama_pasien, jenis_kelamin, tanggal_lahir, alamat, nomor_telepon, email) 
VALUES 
('$data->nama_pasien', '$data->jenis_kelamin', '$data->tanggal_lahir', 
 '$data->alamat', '$data->nomor_telepon')";

if ($conn->query($sql)) {
    echo json_encode(["message" => "Pasien ditambahkan"]);
} else {
    echo json_encode(["error" => $conn->error]);
}
?>
