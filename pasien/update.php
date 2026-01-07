<?php
include '../config.php';

$data = json_decode(file_get_contents("php://input"));

$id = $data->id_pasien;

$sql = "UPDATE pasien SET 
        nama_pasien='$data->nama_pasien',
        jenis_kelamin='$data->jenis_kelamin',
        tanggal_lahir='$data->tanggal_lahir',
        alamat='$data->alamat',
        nomor_telepon='$data->nomor_telepon'
        WHERE id_pasien=$id";

if ($conn->query($sql)) {
    echo json_encode(["message" => "Data pasien diperbarui"]);
} else {
    echo json_encode(["error" => $conn->error]);
}
?>
