<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

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

// ✅ Ambil notifikasi yang dikirim ke provider (bukan jobseeker)
$query = "
    SELECT id, email, title, message, created_at 
    FROM notifications 
    WHERE email = '$email' 
    ORDER BY id DESC
";

$result = $conn->query($query);
$notifications = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
    echo json_encode(["success" => true, "notifications" => $notifications]);
} else {
    echo json_encode(["success" => false, "notifications" => []]);
}

$conn->close();
?>
