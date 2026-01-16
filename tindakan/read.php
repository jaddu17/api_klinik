<?php
include '../config.php';

header("Content-Type: application/json; charset=UTF-8");

$search = isset($_GET['search']) ? trim($_GET['search']) : "";

if ($search !== "") {
    $sql = "SELECT * FROM tindakan WHERE nama_tindakan LIKE ?";
    $stmt = $conn->prepare($sql);
    $param = "%" . $search . "%";
    $stmt->bind_param("s", $param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT * FROM tindakan";
    $result = $conn->query($sql);
}

$output = [];
while ($row = $result->fetch_assoc()) {
    $output[] = $row;
}

echo json_encode($output, JSON_NUMERIC_CHECK);
?>
