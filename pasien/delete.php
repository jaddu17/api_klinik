<?php
include '../config.php';

$id = $_GET['id'];

$sql = "DELETE FROM pasien WHERE id_pasien=$id";

if ($conn->query($sql)) {
    echo json_encode(["message" => "Pasien dihapus"]);
} else {
    echo json_encode(["error" => $conn->error]);
}
?>
