<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$conn = new mysqli("localhost", "root", "", "part_time_db");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Koneksi gagal"]);
    exit;
}

$email = $_POST['email'] ?? '';
if (empty($email) || !isset($_FILES['dokumen'])) {
    echo json_encode(["success" => false, "message" => "Data tidak lengkap"]);
    exit;
}

$target_dir = "uploads/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

$file_name = basename($_FILES["dokumen"]["name"]);
$target_file = $target_dir . $file_name;

if (move_uploaded_file($_FILES["dokumen"]["tmp_name"], $target_file)) {
    $stmt = $conn->prepare("INSERT INTO applications (email_pelamar, dokumen, status) VALUES (?, ?, 'menunggu')");
    $stmt->bind_param("ss", $email, $file_name);
    $stmt->execute();
    echo json_encode(["success" => true, "message" => "Lamaran berhasil dikirim!"]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal upload file"]);
}

$conn->close();
?>
