<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$conn = new mysqli("localhost", "root", "", "part_time_db");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Koneksi database gagal"]);
    exit;
}

$email = $_POST['email'] ?? '';
if (empty($email)) {
    echo json_encode(["success" => false, "message" => "Email tidak boleh kosong"]);
    exit;
}

$sql = "SELECT nama_perusahaan, profil_perusahaan, alamat, no_telepon, email, foto_perusahaan 
        FROM provider_profiles 
        WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$profiles = [];
while ($row = $result->fetch_assoc()) {
    $profiles[] = $row;
}

if (!empty($profiles)) {
    echo json_encode(["success" => true, "profiles" => $profiles]);
} else {
    echo json_encode(["success" => false, "profiles" => []]);
}

$stmt->close();
$conn->close();
?>
