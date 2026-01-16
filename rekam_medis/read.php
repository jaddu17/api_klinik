<?php
header("Content-Type: application/json");
include '../config.php';

$where = "";
$params = [];
$types = "";

// 🔥 Filter berdasarkan id_janji (opsional)
if (isset($_GET['id_janji']) && $_GET['id_janji'] !== '') {
    $where = "WHERE rm.id_janji = ?";
    $params[] = $_GET['id_janji'];
    $types .= "i";
}

$sql = "
SELECT
    rm.id_rekam,
    rm.id_janji,
    rm.id_tindakan,
    rm.diagnosa,
    rm.catatan,
    rm.resep,
    rm.created_at,
    rm.updated_at
FROM rekam_medis rm
$where
ORDER BY rm.id_rekam DESC
";

$stmt = $conn->prepare($sql);

// bind parameter kalau ada filter
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = [
        "id_rekam" => (int) $row["id_rekam"],
        "id_janji" => (int) $row["id_janji"],
        "id_tindakan" => (int) $row["id_tindakan"],
        "diagnosa" => $row["diagnosa"],
        "catatan" => $row["catatan"],
        "resep" => $row["resep"],
        "created_at" => $row["created_at"],
        "updated_at" => $row["updated_at"]
    ];
}

echo json_encode($data);
