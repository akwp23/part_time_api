<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$conn = new mysqli("localhost","root","","part_time_db");
$email_provider = $_POST['email_provider'] ?? '';
$email_pelamar = $_POST['email_pelamar'] ?? '';
$pesan = $_POST['pesan'] ?? '';
$status = $_POST['status'] ?? '';

if(empty($email_pelamar) || empty($pesan)){
  echo json_encode(["success"=>false,"message"=>"Data tidak lengkap"]); exit;
}

$stmt = $conn->prepare("INSERT INTO messages(email_provider,email_pelamar,pesan,status) VALUES (?,?,?,?)");
$stmt->bind_param("ssss",$email_provider,$email_pelamar,$pesan,$status);
$stmt->execute();

$conn->query("UPDATE applications SET status='$status' WHERE email_pelamar='$email_pelamar'");

echo json_encode(["success"=>true,"message"=>"Pesan terkirim"]);
$conn->close();
?>
