<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(0);

$conn = new mysqli("localhost", "root", "", "part_time_db");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Koneksi database gagal"]);
    exit;
}

$email = $_POST['email'] ?? ''; // kirim dari Flutter

if (empty($email)) {
    echo json_encode(["success" => false, "message" => "Email kosong"]);
    exit;
}

$query = "
    SELECT id, email_provider AS email, nama_perusahaan, status, tanggal_update AS tanggal
    FROM notifications_provider
    WHERE email_provider = ?
    ORDER BY tanggal_update DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

echo json_encode(["success" => true, "notifications" => $notifications]);
$conn->close();
?>
