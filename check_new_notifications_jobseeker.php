<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(0);

$conn = new mysqli("localhost", "root", "", "part_time_db");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Koneksi gagal"]);
    exit;
}

$email = $_POST['email'] ?? '';
if (empty($email)) {
    echo json_encode(["success" => false, "message" => "Email kosong"]);
    exit;
}

$query = "SELECT COUNT(*) AS unread FROM notifications_jobseeker WHERE email = ? AND is_read = 0";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

echo json_encode(["success" => true, "unread" => intval($result['unread'])]);
$conn->close();
?>
