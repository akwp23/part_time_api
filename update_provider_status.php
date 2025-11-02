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
$status = $_POST['status'] ?? '';

if (empty($email) || empty($status)) {
    echo json_encode(["success" => false, "message" => "Email dan status wajib diisi"]);
    exit;
}

// 🔍 Cek apakah provider ada di database
$check = $conn->prepare("SELECT nama_perusahaan FROM provider_profiles WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Data provider tidak ditemukan"]);
    $conn->close();
    exit;
}

$row = $result->fetch_assoc();
$nama_perusahaan = $row['nama_perusahaan'] ?? '-';

// ✅ Update status provider
$update = $conn->prepare("UPDATE provider_profiles SET status=? WHERE email=?");
$update->bind_param("ss", $status, $email);
if (!$update->execute()) {
    echo json_encode(["success" => false, "message" => "Gagal memperbarui status provider"]);
    $conn->close();
    exit;
}

// 📨 Simpan notifikasi ke tabel notifications_provider
$tanggal = date('Y-m-d H:i:s');
$insert = $conn->prepare("
    INSERT INTO notifications_provider (email_provider, nama_perusahaan, status, tanggal_update)
    VALUES (?, ?, ?, ?)
");
$insert->bind_param("ssss", $email, $nama_perusahaan, $status, $tanggal);

if ($insert->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Status provider diperbarui dan notifikasi dikirim"
    ]);
} else {
    echo json_encode([
        "success" => true,
        "message" => "Status diperbarui, tapi gagal mengirim notifikasi"
    ]);
}

$conn->close();
?>
