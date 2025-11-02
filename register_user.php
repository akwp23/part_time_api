<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include "config.php";

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$role = trim($_POST['role'] ?? '');

if (empty($email) || empty($password) || empty($role)) {
    echo json_encode(["success" => false, "message" => "Semua field wajib diisi."]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "message" => "Format email tidak valid."]);
    exit;
}

// Enkripsi password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Cek apakah email sudah ada
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Email sudah terdaftar!"]);
    exit;
}

// Simpan user baru
$stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $email, $hashedPassword, $role);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Registrasi berhasil!"]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal registrasi: " . $conn->error]);
}

$stmt->close();
$conn->close();
?>
