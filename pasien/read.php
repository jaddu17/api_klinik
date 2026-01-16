<?php
include '../config.php';

header("Content-Type: application/json; charset=UTF-8");

// ambil keyword dari query string
$keyword = isset($_GET['search']) ? trim($_GET['search']) : "";

// jika ada keyword → pakai WHERE LIKE
if ($keyword !== "") {
    $sql = "SELECT * FROM pasien WHERE nama_pasien LIKE ? OR nomor_telepon LIKE ?";
    $stmt = $conn->prepare($sql);
    $param = "%" . $keyword . "%";
    $stmt->bind_param("ss", $param, $param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // jika tidak ada keyword → ambil semua
    $sql = "SELECT * FROM pasien";
    $result = $conn->query($sql);
}

$output = [];

while ($row = $result->fetch_assoc()) {
    $output[] = [
        "id_pasien" => (int) $row["id_pasien"],
        "nama_pasien" => $row["nama_pasien"],
        "jenis_kelamin" => $row["jenis_kelamin"],
        "tanggal_lahir" => $row["tanggal_lahir"],
        "alamat" => $row["alamat"],
        "nomor_telepon" => $row["nomor_telepon"]
    ];
}

echo json_encode($output, JSON_UNESCAPED_UNICODE);
?>