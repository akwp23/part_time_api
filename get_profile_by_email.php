<?php
include 'connect.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$email = $_POST['email'] ?? '';

if (empty($email)) {
    echo json_encode(["success" => false, "message" => "Email kosong"]);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM profiles WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $profile = $result->fetch_assoc();
    echo json_encode(["success" => true, "profile" => $profile]);
} else {
    echo json_encode(["success" => false, "message" => "Profil tidak ditemukan"]);
}
$stmt->close();
$conn->close();
?>
