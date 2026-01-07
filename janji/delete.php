<?php
include '../config.php';

$id = $_GET['id'];

$sql = "DELETE FROM janji_temu WHERE id_janji=$id";

if ($conn->query($sql)) {
    echo json_encode(["message" => "Janji temu dihapus"]);
} else {
    echo json_encode(["error" => $conn->error]);
}
?>
