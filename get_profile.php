<?php
header("Access-Control-Allow-Origin: *");
include 'connect.php';

$email = $_GET['email'] ?? '';

if (empty($email)) {
    echo json_encode(["success" => false, "message" => "Email tidak boleh kosong"]);
    exit;
}

$result = $conn->query("SELECT * FROM profiles WHERE email='$email'");
if ($result->num_rows > 0) {
    echo json_encode(["success" => true, "data" => $result->fetch_assoc()]);
} else {
    echo json_encode(["success" => false, "message" => "Profil tidak ditemukan"]);
}
$conn->close();
?>
