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
$job_id = $_POST['job_id'] ?? '';
$nama_perusahaan = $_POST['nama_perusahaan'] ?? '';
$pekerjaan = $_POST['pekerjaan'] ?? '';
$alamat = $_POST['alamat'] ?? '';
$hari_jam = $_POST['hari_jam'] ?? '';
$foto_perusahaan = $_POST['foto_perusahaan'] ?? '';

if (empty($email) || empty($job_id)) {
    echo json_encode(["success" => false, "message" => "Data tidak lengkap"]);
    exit;
}

// Cek apakah sudah disimpan sebelumnya
$check = $conn->prepare("SELECT * FROM saved_jobs WHERE email=? AND job_id=?");
$check->bind_param("si", $email, $job_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Sudah disimpan sebelumnya"]);
} else {
    $stmt = $conn->prepare("INSERT INTO saved_jobs (email, job_id, nama_perusahaan, pekerjaan, alamat, hari_jam, foto_perusahaan) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sisssss", $email, $job_id, $nama_perusahaan, $pekerjaan, $alamat, $hari_jam, $foto_perusahaan);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Lowongan disimpan"]);
    } else {
        echo json_encode(["success" => false, "message" => "Gagal menyimpan"]);
    }
}
$conn->close();
?>
