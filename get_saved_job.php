<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(0);

$conn = new mysqli("localhost", "root", "", "part_time_db");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Koneksi database gagal"]);
    exit;
}

$email = $_POST['email'] ?? '';

if (empty($email)) {
    echo json_encode(["success" => false, "message" => "Email kosong"]);
    exit;
}

// Ambil data tersimpan
$query = "SELECT id, job_id, nama_perusahaan, pekerjaan, alamat, hari_jam, foto_perusahaan, tanggal_simpan 
          FROM saved_jobs WHERE email = ? ORDER BY tanggal_simpan DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$saved = [];
while ($row = $result->fetch_assoc()) {
    // Pastikan semua field tidak null
    foreach ($row as $key => $value) {
        if (is_null($value)) $row[$key] = '';
    }
    // Lengkapi path foto
    if (!empty($row['foto_perusahaan']) && strpos($row['foto_perusahaan'], 'uploads/') === false) {
        $row['foto_perusahaan'] = "uploads/" . $row['foto_perusahaan'];
    }
    $saved[] = $row;
}

echo json_encode(["success" => true, "saved" => $saved]);

$conn->close();
?>
