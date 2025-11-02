<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(0);

$conn = new mysqli("localhost", "root", "", "part_time_db");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Koneksi database gagal"]);
    exit;
}

$id = $_POST['id'] ?? '';
$email = $_POST['email'] ?? '';

if (empty($id) || empty($email)) {
    echo json_encode(["success" => false, "message" => "ID atau Email kosong"]);
    exit;
}

// Pastikan id dalam bentuk integer
$id = intval($id);

// Debug log opsional
// file_put_contents("debug_delete.txt", "Delete job id=$id, email=$email\n", FILE_APPEND);

$query = "DELETE FROM provider_jobs WHERE id=? AND email=?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Query gagal disiapkan"]);
    exit;
}

$stmt->bind_param("is", $id, $email);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(["success" => true, "message" => "Lowongan berhasil dihapus"]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Data tidak ditemukan untuk id=$id dan email=$email"
    ]);
}

$stmt->close();
$conn->close();
?>
