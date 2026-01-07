<?php
include '../config.php';

$sql = "SELECT * FROM pasien";
$result = $conn->query($sql);

$output = [];
while ($row = $result->fetch_assoc()) {
    $output[] = [
        "id_pasien"     => (int)$row["id_pasien"],
        "nama_pasien"   => $row["nama_pasien"],
        "jenis_kelamin" => $row["jenis_kelamin"],  // WAJIB ADA
        "tanggal_lahir" => $row["tanggal_lahir"], // WAJIB ADA
        "alamat"        => $row["alamat"],
        "nomor_telepon" => $row["nomor_telepon"]
    ];
}

echo json_encode($output, JSON_UNESCAPED_UNICODE);
?>
