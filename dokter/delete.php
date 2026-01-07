<?php
include '../config.php';

$id = $_GET['id'];

$sql = "DELETE FROM dokter WHERE id_dokter=$id";

if ($conn->query($sql)) {
    echo json_encode(["message" => "Dokter dihapus"]);
} else {
    echo json_encode(["error" => $conn->error]);
}
?>
