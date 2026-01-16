<?php
include 'config.php';
$res = $conn->query("DESCRIBE pasien");
$cols = [];
while ($row = $res->fetch_assoc()) {
    $cols[] = $row;
}
echo json_encode($cols);
?>