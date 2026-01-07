<?php
header("Content-Type: application/json");
require_once "../config.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['nama_tindakan']) || !isset($data['deskripsi']) || !isset($data['harga'])) {
    echo json_encode(["message" => "Invalid input"]);
    exit;
}

$nama = $data['nama_tindakan'];
$deskripsi = $data['deskripsi'];
$biaya = $data['harga'];

$query = "INSERT INTO tindakan (nama_tindakan, deskripsi, harga) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssi", $nama, $deskripsi, $biaya);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}
?>
