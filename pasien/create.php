<?php
include '../config.php';

header("Content-Type: application/json; charset=UTF-8");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["error" => "Data tidak valid atau kosong"]);
    exit;
}

// ================= VALIDASI MANUAL =================

// Nama: allow alphabets, spaces, dots, and commas
if (!isset($data['nama_pasien']) || !preg_match("/^[A-Za-z\s\.,']+$/", $data['nama_pasien'])) {
    echo json_encode(["error" => "Nama pasien tidak valid"]);
    exit;
}

// Nomor telepon hanya angka
if (!isset($data['nomor_telepon']) || !preg_match("/^[0-9]+$/", $data['nomor_telepon'])) {
    echo json_encode(["error" => "Nomor telepon hanya boleh berisi angka"]);
    exit;
}

// Nomor telepon 11–13 digit
$len = strlen($data['nomor_telepon']);
if ($len < 11 || $len > 13) {
    echo json_encode(["error" => "Nomor telepon harus 11 sampai 13 digit"]);
    exit;
}

// ================= INSERT DATA =================

// Escape data to prevent SQL injection
$nama = $conn->real_escape_string($data['nama_pasien']);
$jk = $conn->real_escape_string($data['jenis_kelamin']);
$tgl = $conn->real_escape_string($data['tanggal_lahir']);
$alamat = $conn->real_escape_string($data['alamat']);
$telp = $conn->real_escape_string($data['nomor_telepon']);

$sql = "INSERT INTO pasien (nama_pasien, jenis_kelamin, tanggal_lahir, alamat, nomor_telepon) 
        VALUES ('$nama', '$jk', '$tgl', '$alamat', '$telp')";

if ($conn->query($sql)) {
    echo json_encode(["message" => "Pasien berhasil ditambahkan"]);
} else {
    $error = $conn->error;
    $msg = "Gagal menambahkan pasien: " . $error;

    if (strpos($error, 'uq_pasien_nama') !== false) {
        $msg = "Nama pasien sudah terdaftar";
    } elseif (strpos($error, 'uq_pasien_alamat') !== false) {
        $msg = "Alamat pasien sudah digunakan";
    } elseif (strpos($error, 'uq_pasien_telp') !== false) {
        $msg = "Nomor telepon sudah terdaftar";
    }

    echo json_encode(["error" => $msg]);
}
?>