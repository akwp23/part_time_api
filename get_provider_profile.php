<?php
require 'connect.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(0);

$email = $_POST['email'] ?? '';
if ($email === '') {
  echo json_encode(["success" => false, "message" => "Email wajib ada"]);
  exit;
}

$stmt = $conn->prepare("SELECT email, nama_perusahaan, profil_perusahaan, alamat, no_telepon, foto_perusahaan, 
                               COALESCE(status,'pending') AS status
                        FROM provider_profiles WHERE email=? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($row = $res->fetch_assoc()) {
  // rapikan path foto
  if (!empty($row['foto_perusahaan'])) {
    $fp = $row['foto_perusahaan'];
    if (strpos($fp, 'http') !== 0) {
      $fp = (strpos($fp, 'uploads/') === 0) ? $fp : "uploads/".$fp;
    }
    $row['foto_perusahaan'] = $fp;
  }
  echo json_encode(["success" => true, "data" => $row]);
} else {
  echo json_encode(["success" => false, "message" => "Profil tidak ditemukan"]);
}
$conn->close();
