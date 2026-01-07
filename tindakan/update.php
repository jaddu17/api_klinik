<?php
// Aktifkan error reporting untuk debugging (hapus di production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // jangan tampilkan ke user
ini_set('log_errors', 1);

header('Content-Type: application/json; charset=utf-8'); // ✅ WAJIB

require '../config.php';

// Ambil input JSON
$input = file_get_contents('php://input');
if (!$input) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "No input data"]);
    exit;
}

$data = json_decode($input, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid JSON"]);
    exit;
}

$id = $data["id_tindakan"] ?? 0;
$nama = trim($data["nama_tindakan"] ?? "");
$deskripsi = trim($data["deskripsi"] ?? "");
$harga = trim($data["harga"] ?? "0");

// Validasi
if ($id <= 0 || empty($nama)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Data tidak valid"]);
    exit;
}

// Query UPDATE
$query = "UPDATE tindakan SET nama_tindakan = ?, harga = ?, deskripsi = ? WHERE id_tindakan = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
    exit;
}

// Bind parameter: semua string kecuali ID
$stmt->bind_param("sssi", $nama, $harga, $deskripsi, $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "Tindakan berhasil diperbarui"]);
    } else {
        echo json_encode(["success" => false, "message" => "Tidak ada perubahan (data sama)"]);
    }
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Execute error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>