<?php
include '../config.php';

// Set header JSON
header('Content-Type: application/json; charset=UTF-8');

// Ambil parameter pencarian (opsional)
$search = $_GET['search'] ?? '';

// Escape input untuk mencegah SQL injection sebisa mungkin
$search = $conn->real_escape_string($search);

// Bangun query dasar
$sql = "SELECT id_dokter, nama_dokter, spesialisasi, nomor_telepon FROM dokter";

// Tambahkan filter pencarian jika ada
if (!empty($search)) {
    $sql .= " WHERE nama_dokter LIKE '%$search%' OR spesialisasi LIKE '%$search%'";
}

// Urutkan hasil
$sql .= " ORDER BY nama_dokter ASC";

$result = $conn->query($sql);

$output = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $output[] = [
            "id_dokter" => (int) $row["id_dokter"],
            "nama_dokter" => $row["nama_dokter"],
            "spesialisasi" => $row["spesialisasi"],
            "nomor_telepon" => $row["nomor_telepon"]
        ];
    }
}

echo json_encode($output, JSON_UNESCAPED_UNICODE);
?>