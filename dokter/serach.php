<?php
include '../config.php';

// Set header JSON
header('Content-Type: application/json; charset=UTF-8');

// Ambil parameter 'search' dari query string (opsional)
$search = $_GET['search'] ?? '';

// Escape input untuk mencegah SQL injection sebisa mungkin (minimal protection)
$search = $conn->real_escape_string($search);

// Bangun query
if (!empty($search)) {
    $sql = "SELECT id_dokter, nama_dokter, spesialisasi, nomor_telepon 
            FROM dokter 
            WHERE nama_dokter LIKE '%$search%' OR spesialisasi LIKE '%$search%'
            ORDER BY nama_dokter ASC";
} else {
    $sql = "SELECT id_dokter, nama_dokter, spesialisasi, nomor_telepon 
            FROM dokter 
            ORDER BY nama_dokter ASC";
}

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $dokterList = [];
    while ($row = $result->fetch_assoc()) {
        // Pastikan id_dokter dikirim sebagai integer (bukan string)
        $row['id_dokter'] = (int) $row['id_dokter'];
        $dokterList[] = $row;
    }
    echo json_encode($dokterList);
} else {
    echo json_encode([]);
}
?>