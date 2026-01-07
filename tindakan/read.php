<?php
include '../config.php';

$sql = "SELECT * FROM tindakan";
$result = $conn->query($sql);

$output = [];
while ($row = $result->fetch_assoc()) {
    $output[] = $row;
}

echo json_encode($output, JSON_NUMERIC_CHECK);
?>