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

if (empty($email) || empty($job_id)) {
    echo json_encode(["success" => false, "message" => "Email atau Job ID kosong"]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM saved_jobs WHERE email = ? AND job_id = ?");
$stmt->bind_param("ss", $email, $job_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Lowongan berhasil dihapus"]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal menghapus lowongan"]);
}

$stmt->close();
$conn->close();
?>
