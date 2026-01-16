<?php
header("Content-Type: application/json");
include '../config.php';

$data = json_decode(file_get_contents("php://input"), true);

// Validasi input dasar
if (
    empty($data['nama_dokter']) ||
    empty($data['spesialisasi']) ||
    empty($data['nomor_telepon'])
) {
    http_response_code(400);
    echo json_encode(["error" => "Semua data dokter wajib diisi"]);
    exit; // ← HARUS ADA exit
}

$nama = trim($data['nama_dokter']);
$spesialisasi = trim($data['spesialisasi']);
$telp = trim($data['nomor_telepon']);

// Validasi nomor telepon
if (!ctype_digit($telp)) {
    http_response_code(400);
    echo json_encode(["error" => "Nomor telepon hanya boleh berisi angka"]);
    exit;
}
if (substr($telp, 0, 2) !== "08") {
    http_response_code(400);
    echo json_encode(["error" => "Nomor telepon harus diawali dengan '08'"]);
    exit;
}
if (strlen($telp) < 11 || strlen($telp) > 13) {
    http_response_code(400);
    echo json_encode(["error" => "Nomor telepon harus terdiri dari 11–13 digit"]);
    exit;
}

// ✅ CEK UNIK NAMA DOKTER
$cek = $conn->prepare("SELECT id_dokter FROM dokter WHERE nama_dokter = ?");
$cek->bind_param("s", $nama);
$cek->execute();
$result = $cek->get_result();

if ($result->num_rows > 0) {
    http_response_code(409);
    echo json_encode(["error" => "Nama dokter sudah terdaftar"]);
    exit; // ← INI YANG SERING LUPA! HARUS ADA!
}

// ✅ INSERT HANYA JIKA TIDAK DUPILKAT
$stmt = $conn->prepare("INSERT INTO dokter (nama_dokter, spesialisasi, nomor_telepon) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $nama, $spesialisasi, $telp);

if ($stmt->execute()) {
    echo json_encode(["message" => "Dokter berhasil ditambahkan"]);
} else {
    // Error saat insert (misal: constraint lain)
    http_response_code(500);
    echo json_encode(["error" => "Gagal menyimpan data"]);
}
?>