<?php
require '../config.php';

$id = $_GET["id"] ?? 0;

if ($id == 0) {
    echo json_encode(["success" => false, "message" => "ID tidak valid"]);
    exit;
}

$query = "DELETE FROM tindakan WHERE id_tindakan = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Tindakan berhasil dihapus"]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal menghapus tindakan"]);
}
?>
