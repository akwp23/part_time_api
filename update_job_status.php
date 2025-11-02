<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$conn = new mysqli("localhost", "root", "", "part_time_db");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Koneksi database gagal"]);
    exit;
}

$id = $_POST['id'] ?? '';
$status = $_POST['status'] ?? '';

if (empty($id) || empty($status)) {
    echo json_encode(["success" => false, "message" => "Data tidak lengkap"]);
    exit;
}

// 🔹 Ambil email & nama perusahaan
$stmt = $conn->prepare("SELECT email, nama_perusahaan FROM provider_jobs WHERE id=? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode(["success" => false, "message" => "Lowongan tidak ditemukan"]);
    exit;
}

$provider = $result->fetch_assoc();
$emailProvider = $provider['email'];
$namaPerusahaan = $provider['nama_perusahaan'];
$stmt->close();

// 🔹 Update status lowongan
$stmt2 = $conn->prepare("UPDATE provider_jobs SET status=? WHERE id=?");
$stmt2->bind_param("si", $status, $id);
$stmt2->execute();
$stmt2->close();

// 🔹 Simpan notifikasi (kalau tabelnya ada)
$conn->query("
    INSERT INTO notifications (email, title, message, created_at)
    VALUES (
        '$emailProvider',
        'Status Lowongan dari Admin',
        'Lowongan Anda ($namaPerusahaan) telah $status oleh admin.',
        NOW()
    )
");

echo json_encode([
    "success" => true,
    "message" => "Status lowongan '$namaPerusahaan' berhasil diperbarui menjadi '$status'."
]);

$conn->close();
?>
